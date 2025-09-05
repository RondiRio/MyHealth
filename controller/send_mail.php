<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';

function enviarEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rondi.rio@gmail.com'; 
        $mail->Password   = 'idse ultj ayno gzvy'; // ⚠️ Ideal mover isso para .env
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

    } catch (Exception $e) {
        // Log do erro (não exibir detalhes sensíveis)
        error_log("Erro ao enviar email: " . $e->getMessage());
        return false;
    }
}
?>