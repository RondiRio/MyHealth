<?php
require_once 'iniciar.php';

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - MyHealth</title>
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

        .card-recuperar {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
        }

        .card-body {
            background: rgba(255, 255, 255, 0.95);
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
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.jpg" alt="Logo MyHealth" width="140" height="70" class="d-inline-block align-text-top">
            </a>
        </div>
    </nav>
</header>

<main class="d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card card-recuperar shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="fas fa-unlock-alt fa-2x text-primary"></i>
                            </div>
                            <h1 class="h4 fw-bold text-dark">Recuperar Senha</h1>
                            <p class="text-muted">Informe seu e-mail, CPF ou CRM para receber as instruções.</p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-<?= $notificacao['tipo'] ?? 'danger' ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="processa_recuperar.php" method="POST" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">

                            <div class="form-floating mb-3">
                                <input type="text" id="usuario" name="usuario" class="form-control" required placeholder="E-mail, CPF ou CRM">
                                <label for="usuario">
                                    <i class="fas fa-envelope me-2"></i>E-mail, CPF ou CRM
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar instruções
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="login.php" class="text-primary fw-bold text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Voltar para o login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.card-recuperar');
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
</body>
</html>
