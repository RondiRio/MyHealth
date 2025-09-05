<?php
include_once('iniciar.php');
include_once('send_mail.php');
include_once('access.Google.php');


// 
// $GetPassTrue = new getPassTrue($passTrue);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // Excluir tokens expirados (mais de 15 minutos)
    $stmt = $db->prepare("DELETE FROM password_resets WHERE created_at < NOW() - INTERVAL 15 MINUTE");
    $stmt->execute();
}

// ==== ENTRADA DO USUÁRIO ====
$usuario = trim($_POST['usuario'] ?? '');

if (!$usuario) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Informe CPF, CRM ou Email'];
    header('Location: ../publics/recuperar_senha.php');
    exit;
}

// ==== CONEXÃO COM BANCO ====
$db = $conexaoBD->getConexao();

// ==== DEFINIÇÃO DA QUERY - CORRIGINDO NOMES DAS TABELAS ====
if (filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
    $sql = "SELECT email FROM user_pacientes WHERE email = ? 
            UNION 
            SELECT email FROM user_medicos WHERE email = ?";
    $params = [$usuario, $usuario];
} elseif (preg_match('/^\d{11}$/', $usuario)) {
    $sql = "SELECT email FROM user_pacientes WHERE cpf = ?";
    $params = [$usuario];
} else {
    $sql = "SELECT email FROM user_medicos WHERE crm = ?";
    $params = [$usuario];
}

// ==== EXECUÇÃO DA QUERY ====
$stmt = $db->prepare($sql);
$tipos = str_repeat("s", count($params));
$stmt->bind_param($tipos, ...$params);
$stmt->execute();

$result = $stmt->get_result();
$dados = $result->fetch_assoc();

if (!$dados) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Usuário não encontrado.'];
    header("Location: ../publics/recuperar_senha.php");
    exit;
}

$email = $dados['email'];

// ==== GERAÇÃO DO TOKEN ====
$token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// ==== LIMPAR TOKENS ANTIGOS ANTES DE INSERIR NOVO ====
$stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// ==== SALVAR TOKEN NO BANCO ====
$stmt = $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $email, $token);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';
// ==== ENVIO DE EMAIL ====
if ($stmt->execute()) {

    // Configuração do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rondi.rio@gmail.com'; 
        $mail->Password   = '$passTrue'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Configuração de charset
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Configuração do remetente e destinatário
        $mail->setFrom('rondi.rio@gmail.com', 'MyHealth - Recuperação');
        $mail->addAddress($email);
        $mail->addReplyTo('rondi.rio@gmail.com', 'MyHealth Support');

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Código de Recuperação de Senha - MyHealth';
        $mail->Body    = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .token { background: #f8fafc; border: 2px dashed #2563eb; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
                .token h2 { color: #2563eb; font-size: 36px; margin: 0; letter-spacing: 8px; }
                .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 MyHealth</h1>
                    <p>Recuperação de Senha</p>
                </div>
                <div class='content'>
                    <h2>Olá!</h2>
                    <p>Você solicitou a recuperação de sua senha. Use o código abaixo para prosseguir:</p>
                    
                    <div class='token'>
                        <p>Seu código de verificação:</p>
                        <h2>$token</h2>
                    </div>
                    
                    <p><strong>⏰ Este código expira em 15 minutos.</strong></p>
                    <p>Se você não solicitou esta recuperação, ignore este e-mail.</p>
                    
                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #64748b;'>
                        Por segurança, nunca compartilhe este código com outras pessoas.
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 MyHealth. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>";

        // Versão texto alternativo
        $mail->AltBody = "
        MyHealth - Recuperação de Senha
        
        Olá,
        
        Você solicitou a recuperação de sua senha.
        Use o seguinte código para redefinir sua senha: $token
        
        Este código expira em 15 minutos.
        
        Se você não solicitou esta recuperação, ignore este e-mail.
        
        MyHealth Team
        ";

        // Enviar
        $mail->send();
        return true;
    if(enviarEmail($email, $token)){
        $_SESSION['notificacao'] = ['tipo' => 'success', 'mensagem' => 'Um código foi enviado para o seu e-mail.'];
        header("Location: ../publics/validar_token.php?email=" . urlencode($email));
        exit;
    } else {
        $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao enviar e-mail.'];
        header("Location: ../publics/recuperar_senha.php");
        exit;
    }
} else {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Erro interno. Tente novamente.'];
    header("Location: ../publics/recuperar_senha.php");
    exit;
}

?>