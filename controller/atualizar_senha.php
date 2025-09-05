<?php
require_once 'iniciar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Acesso inválido.'];
    header("Location: ../publics/recuperar_senha.php");
    exit;
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// Validar campos obrigatórios
if (!$email || !$senha || !$confirmar) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Todos os campos são obrigatórios.'];
    header("Location: ../publics/nova_senha.php?email=" . urlencode($email));
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'E-mail inválido.'];
    header("Location: ../publics/nova_senha.php?email=" . urlencode($email));
    exit;
}

// Verificar se as senhas coincidem
if ($senha !== $confirmar) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'As senhas não coincidem.'];
    header("Location: ../publics/nova_senha.php?email=" . urlencode($email));
    exit;
}

// Validar força da senha: mínimo 8, uma maiúscula, uma minúscula, um número e um especial
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $senha)) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'A senha deve ter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e símbolo.'];
    header("Location: ../publics/nova_senha.php?email=" . urlencode($email));
    exit;
}

// Gerar hash da senha
$hash = password_hash($senha, PASSWORD_DEFAULT);

$db = $conexaoBD->getConexao();

// Primeiro tenta atualizar na tabela paciente
$stmt = $db->prepare("UPDATE users_paciente SET senha_hash = ? WHERE email = ?");
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

$linhasAfetadas = $stmt->affected_rows;

// Se não atualizou nenhuma linha na tabela paciente, tenta na tabela médico
if ($linhasAfetadas === 0) {
    $stmt = $db->prepare("UPDATE users_medico SET senha_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    $stmt->execute();
    $linhasAfetadas = $stmt->affected_rows;
}

// Verificar se a atualização foi bem-sucedida
if ($linhasAfetadas > 0) {
    $_SESSION['notificacao'] = ['tipo' => 'success', 'mensagem' => 'Senha atualizada com sucesso.'];
    header("Location: ../publics/login.php");
    exit;
} else {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'E-mail não encontrado ou erro na atualização.'];
    header("Location: ../publics/nova_senha.php?email=" . urlencode($email));
    exit;
}
?>