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
    <title>Validar Código - MyHealth</title>
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
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: 600;
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

        .timer {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            margin: 15px 0;
            font-size: 0.9rem;
            color: #92400e;
        }

        .resend-code {
            margin-top: 15px;
            text-align: center;
        }

        .resend-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            text-decoration: underline;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .resend-btn:disabled {
            color: #9ca3af;
            cursor: not-allowed;
            text-decoration: none;
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
                                <i class="fas fa-key fa-2x text-primary"></i>
                            </div>
                            <h1 class="h4 fw-bold text-dark">Digite o Código</h1>
                            <p class="text-muted">
                                Enviamos um código de 6 dígitos para:<br>
                                <strong><?= htmlspecialchars($email) ?></strong>
                            </p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-<?= $notificacao['tipo'] ?? 'danger' ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?= $notificacao['tipo'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                                <?= htmlspecialchars($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../controller/validar_token_action.php" method="POST" id="tokenForm">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                            <?php if (isset($seguranca)): ?>
                                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                            <?php endif; ?>

                            <div class="form-floating mb-3">
                                <input 
                                    type="text" 
                                    id="token" 
                                    name="token" 
                                    class="form-control" 
                                    required 
                                    maxlength="6" 
                                    pattern="\d{6}" 
                                    placeholder="000000"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                >
                                <label for="token"><i class="fas fa-lock me-2"></i>Código de 6 dígitos</label>
                            </div>

                            <div class="timer" id="timer">
                                <i class="fas fa-clock me-2"></i>
                                <span>Código válido por: <strong id="countdown">15:00</strong></span>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-check me-2"></i>Validar Código
                                </button>
                            </div>
                        </form>

                        <div class="resend-code">
                            <p class="text-muted mb-2">Não recebeu o código?</p>
                            <button class="resend-btn" id="resendBtn" onclick="resendCode()" disabled>
                                <i class="fas fa-paper-plane me-1"></i>Reenviar código
                            </button>
                            <p id="resendTimer" class="text-muted mt-2" style="font-size: 0.85rem;"></p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="recuperar_senha.php" class="text-primary fw-bold text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Tentar com outro e-mail
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
    const tokenInput = document.getElementById('token');
    const submitBtn = document.getElementById('submitBtn');
    const countdownEl = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    
    let timeLeft = 15 * 60; // 15 minutos em segundos
    let resendTimeLeft = 60; // 1 minuto para reenvio
    
    // Formatação do token - apenas números
    tokenInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 6) value = value.substring(0, 6);
        e.target.value = value;
        
        // Habilitar/desabilitar botão
        submitBtn.disabled = value.length !== 6;
        
        // Auto-submit quando completar 6 dígitos
        if (value.length === 6) {
            setTimeout(() => {
                document.getElementById('tokenForm').submit();
            }, 500);
        }
    });

    // Countdown timer
    function updateCountdown() {
        if (timeLeft <= 0) {
            countdownEl.textContent = "Expirado";
            countdownEl.parentElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i><span class="text-danger">Código expirado</span>';
            submitBtn.disabled = true;
            submitBtn.textContent = "Código Expirado";
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        timeLeft--;
    }

    // Resend countdown
    function updateResendCountdown() {
        if (resendTimeLeft <= 0) {
            resendBtn.disabled = false;
            resendTimer.textContent = "";
            return;
        }
        
        resendTimer.textContent = `Reenviar disponível em ${resendTimeLeft}s`;
        resendTimeLeft--;
    }

    // Iniciar timers
    updateCountdown();
    updateResendCountdown();
    
    const countdownInterval = setInterval(updateCountdown, 1000);
    const resendInterval = setInterval(updateResendCountdown, 1000);

    // Limpar intervalos quando necessário
    setTimeout(() => {
        clearInterval(countdownInterval);
    }, 15 * 60 * 1000);
    
    setTimeout(() => {
        clearInterval(resendInterval);
    }, 60 * 1000);

    // Função para reenviar código
    window.resendCode = function() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reenviando...';
        
        // Simular requisição de reenvio
        fetch('../controller/processa_recuperar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `usuario=${encodeURIComponent('<?= $email ?>')}&csrf_token=${document.querySelector('input[name="csrf_token"]')?.value || ''}`
        })
        .then(response => {
            if (response.ok) {
                // Reset timers
                timeLeft = 15 * 60;
                resendTimeLeft = 60;
                resendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Reenviar código';
                
                // Mostrar mensagem de sucesso
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                alert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Código reenviado com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.card-body').insertBefore(alert, document.querySelector('form'));
                
                // Remover alerta após 5 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            } else {
                throw new Error('Erro no reenvio');
            }
        })
        .catch(error => {
            resendBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Erro no reenvio';
            setTimeout(() => {
                resendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Reenviar código';
                resendBtn.disabled = false;
            }, 3000);
        });
    };

    // Animação inicial
    const card = document.querySelector('.card-recuperar');
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        card.style.transition = 'all 0.6s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    }, 100);

    // Focus no input
    tokenInput.focus();
});
</script>
</body>
</html>