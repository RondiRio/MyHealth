<?php
require_once '../controller/iniciar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);

$email = $_GET['email'] ?? null;
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Acesso inválido.'];
    header("Location: recuperar_senha.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --light-bg: #f8fafc;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
            font-family: 'Inter', sans-serif;
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

        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e2e8f0;
            overflow: hidden;
            margin: 8px 0;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .requirements {
            font-size: 0.85rem;
            margin-top: 10px;
        }

        .requirement {
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .requirement.met {
            color: var(--success-color);
        }

        .requirement i {
            width: 16px;
            margin-right: 6px;
        }
    </style>
</head>
<body>

<?= include_once("../routes/header.phtml")?>

<main class="d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card card-recuperar">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                <i class="fas fa-shield-alt fa-2x text-primary"></i>
                            </div>
                            <h1 class="h4 fw-bold text-dark">Criar Nova Senha</h1>
                            <p class="text-muted">Digite uma senha forte para proteger sua conta.</p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-<?= $notificacao['tipo'] ?? 'danger' ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?= $notificacao['tipo'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                                <?= htmlspecialchars($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../controller/atualizar_senha.php" method="POST" id="passwordForm">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                            <?php if (isset($seguranca)): ?>
                                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                            <?php endif; ?>

                            <div class="form-floating mb-3">
                                <input type="password" id="senha" name="senha" class="form-control" required placeholder="Senha">
                                <label for="senha"><i class="fas fa-lock me-2"></i>Nova Senha</label>
                                
                                <div class="password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-fill" id="strengthFill"></div>
                                    </div>
                                    <small id="strengthText" class="text-muted">Digite uma senha</small>
                                </div>
                            </div>

                            <div class="requirements mb-3">
                                <div class="requirement" id="req-length">
                                    <i class="fas fa-times"></i> Mínimo 8 caracteres
                                </div>
                                <div class="requirement" id="req-lowercase">
                                    <i class="fas fa-times"></i> Pelo menos uma letra minúscula
                                </div>
                                <div class="requirement" id="req-uppercase">
                                    <i class="fas fa-times"></i> Pelo menos uma letra maiúscula
                                </div>
                                <div class="requirement" id="req-number">
                                    <i class="fas fa-times"></i> Pelo menos um número
                                </div>
                                <div class="requirement" id="req-special">
                                    <i class="fas fa-times"></i> Pelo menos um caractere especial (@$!%*?&)
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" id="confirmar" name="confirmar" class="form-control" required placeholder="Confirmar Senha">
                                <label for="confirmar"><i class="fas fa-lock me-2"></i>Confirmar Senha</label>
                                <div id="confirmMessage" class="mt-2" style="font-size: 0.85rem;"></div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-save me-2"></i>Atualizar Senha
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="login.php" class="text-primary fw-bold text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Voltar ao login
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
    const senhaInput = document.getElementById('senha');
    const confirmarInput = document.getElementById('confirmar');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const submitBtn = document.getElementById('submitBtn');
    const confirmMessage = document.getElementById('confirmMessage');

    const requirements = {
        length: { element: document.getElementById('req-length'), regex: /.{8,}/ },
        lowercase: { element: document.getElementById('req-lowercase'), regex: /[a-z]/ },
        uppercase: { element: document.getElementById('req-uppercase'), regex: /[A-Z]/ },
        number: { element: document.getElementById('req-number'), regex: /\d/ },
        special: { element: document.getElementById('req-special'), regex: /[@$!%*?&]/ }
    };

    function updateStrength(password) {
        let score = 0;
        let metRequirements = 0;

        for (const [key, req] of Object.entries(requirements)) {
            if (req.regex.test(password)) {
                req.element.classList.add('met');
                req.element.querySelector('i').className = 'fas fa-check';
                metRequirements++;
            } else {
                req.element.classList.remove('met');
                req.element.querySelector('i').className = 'fas fa-times';
            }
        }

        score = (metRequirements / 5) * 100;
        
        strengthFill.style.width = score + '%';
        
        if (score < 40) {
            strengthFill.style.background = '#ef4444';
            strengthText.textContent = 'Senha fraca';
            strengthText.className = 'text-danger';
        } else if (score < 80) {
            strengthFill.style.background = '#f59e0b';
            strengthText.textContent = 'Senha média';
            strengthText.className = 'text-warning';
        } else {
            strengthFill.style.background = '#10b981';
            strengthText.textContent = 'Senha forte';
            strengthText.className = 'text-success';
        }

        return metRequirements === 5;
    }

    function updateConfirmation() {
        const senha = senhaInput.value;
        const confirmar = confirmarInput.value;

        if (confirmar === '') {
            confirmMessage.textContent = '';
            return false;
        }

        if (senha === confirmar) {
            confirmMessage.innerHTML = '<i class="fas fa-check text-success me-1"></i><span class="text-success">Senhas coincidem</span>';
            return true;
        } else {
            confirmMessage.innerHTML = '<i class="fas fa-times text-danger me-1"></i><span class="text-danger">Senhas não coincidem</span>';
            return false;
        }
    }

    function updateSubmitButton() {
        const passwordValid = updateStrength(senhaInput.value);
        const confirmValid = updateConfirmation();
        
        if (passwordValid && confirmValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    senhaInput.addEventListener('input', updateSubmitButton);
    confirmarInput.addEventListener('input', updateSubmitButton);

    // Animação inicial
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