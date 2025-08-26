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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --light-bg: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            backdrop-filter: blur(10px);
        }

        .navbar-brand img {
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .card-login {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
        }

        .card-login img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .card-body {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .form-floating > .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .form-floating > label {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.875rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .text-decoration-none:hover {
            text-decoration: underline !important;
        }

        .login-image-overlay {
            background: linear-gradient(45deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.6));
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        @media (max-width: 767.98px) {
            .card-login {
                border-radius: 16px;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.jpg" alt="Logo MyHealth" width="140" height="70" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-2"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="servicos.php">
                            <i class="fas fa-stethoscope me-2"></i>Serviços
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sobre.php">
                            <i class="fas fa-info-circle me-2"></i>Sobre Nós
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contato.php">
                            <i class="fas fa-envelope me-2"></i>Contato
                        </a>
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
                        <div class="col-md-6 d-none d-md-block position-relative">
                            <img src="images/Prontuário Digital.jpg"
                                 alt="Médica analisando prontuário digital"
                                 class="img-fluid h-100">
                            <div class="login-image-overlay">
                                <div>
                                    <i class="fas fa-stethoscope fa-3x mb-3"></i>
                                    <h3 class="fw-bold">Bem-vindo ao MyHealth</h3>
                                    <p class="mb-0">Sua plataforma de saúde preventiva</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                                    </div>
                                    <h1 class="h3 fw-bold text-dark">Bem-vindo(a) de volta!</h1>
                                    <p class="text-muted">Acesse sua conta para continuar.</p>
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
                                            <option value="paciente" >Paciente</option>
                                            <option value="medico" selected="true">Médico</option>
                                        </select>
                                        <label for="tipo_usuario">
                                            <i class="fas fa-user-tag me-2"></i>Eu sou...
                                        </label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" id="identificador" name="identificador" class="form-control" value="medico" required placeholder="CRM ou CPF">
                                        <label for="identificador">
                                            <i class="fas fa-id-card me-2"></i>CRM ou CPF
                                        </label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" id="senha" name="senha" class="form-control" required placeholder="Senha">
                                        <label for="senha">
                                            <i class="fas fa-lock me-2"></i>Senha
                                        </label>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center my-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                                            <label class="form-check-label text-muted" for="flexCheckDefault">
                                                Lembrar-me
                                            </label>
                                        </div>
                                        <a href="recuperar_senha.php" class="text-primary text-decoration-none fw-medium">
                                            Esqueceu a senha?
                                        </a>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Não tem uma conta? 
                                        <a href="cadastrar.php" class="text-primary fw-bold text-decoration-none">
                                            Cadastre-se agora
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Adiciona animação de entrada
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.card-login');
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });

    // Atualiza placeholder baseado no tipo de usuário
    document.getElementById('tipo_usuario').addEventListener('change', function() {
        const identificadorInput = document.getElementById('identificador');
        const label = document.querySelector('label[for="identificador"]');
        
        if (this.value === 'medico') {
            identificadorInput.placeholder = 'Digite seu CRM';
            label.innerHTML = '<i class="fas fa-id-card me-2"></i>CRM';
        } else {
            identificadorInput.placeholder = 'Digite seu CPF';
            label.innerHTML = '<i class="fas fa-id-card me-2"></i>CPF';

            // Adiciona máscara para CPF
            identificadorInput.addEventListener('input', function() {
                let value = identificadorInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos
                value = value.replace(/(\d{3})(\d)/, '$1.$2'); // Adiciona o primeiro ponto
                value = value.replace(/(\d{3})(\d)/, '$1.$2'); // Adiciona o segundo ponto
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); // Adiciona o traço
                identificadorInput.value = value;
            });
        }
    });
</script>
</body>
</html>