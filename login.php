<?php
require_once 'iniciar.php';

if (isset($_SESSION['usuario_id'])) {
    $dashboard = ($_SESSION['tipo_usuario'] === 'medico') ? 'dashboard_medico.php' : 'dashBoard_Paciente.php';
    header("Location: " . $dashboard);
    exit;
}

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f1f5f9;
            font-family: 'Poppins', sans-serif;
        }
        .card-login {
            border-radius: 1rem;
            border: none;
            overflow: hidden;
        }
        .card-login img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }
        @media (max-width: 767.98px) {
            .card-login {
                border-radius: .75rem;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.jpg" alt="Logo MyHealth" width="150" height="75" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="servicos.php">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sobre.php">Sobre Nós</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contato.php">Contato</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card card-login shadow-lg">
                    <div class="row g-0">
                        <div class="col-md-6 d-none d-md-block">
                            <img src="images/Prontuário Digital.jpg"
                                 alt="Médica analisando prontuário digital"
                                 class="img-fluid h-100">
                        </div>
                        <div class="col-md-6">
                            <div class="card-body p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <img src="images/logo.jpg" alt="Logo MyHealth" width="60" class="mb-3 rounded-circle">
                                    <h1 class="h3 fw-bold">Bem-vindo(a) de volta!</h1>
                                    <p class="text-muted small">Acesse sua conta para continuar.</p>
                                </div>
                                <?php if ($notificacao): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <form action="Valida_login.php" method="POST" autocomplete="off">
                                    <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                                    <div class="form-floating mb-3">
                                        <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                            <option value="medico">Médico</option>
                                            <option value="paciente">Paciente</option>
                                        </select>
                                        <label for="tipo_usuario">Eu sou...</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" id="identificador" name="identificador" class="form-control" required placeholder="CRM ou CPF">
                                        <label for="identificador">CRM ou CPF</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" id="senha" name="senha" class="form-control" required placeholder="Senha">
                                        <label for="senha">Senha</label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center my-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                                            <label class="form-check-label small" for="flexCheckDefault">Lembrar-me</label>
                                        </div>
                                        <a href="recuperar_senha.php" class="small">Esqueceu a senha?</a>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg fw-bold">Entrar</button>
                                    </div>
                                </form>
                                <p class="text-center text-muted mt-4 mb-0 small">Não tem uma conta? <a href="cadastrar.php" class="fw-bold text-decoration-none">Cadastre-se</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
