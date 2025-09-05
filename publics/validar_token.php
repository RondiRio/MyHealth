<?php
require_once '../controller/iniciar.php';

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);

$email = $_GET['email'] ?? null;
if (!$email) {
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
        body { min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); font-family: 'Inter', sans-serif; }
        .card-recuperar { border-radius: 24px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05); }
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
                            <i class="fas fa-key fa-2x text-primary mb-3"></i>
                            <h1 class="h4 fw-bold text-dark">Digite o Código</h1>
                            <p class="text-muted">Enviamos um código de 6 dígitos para <b><?= htmlspecialchars($email) ?></b>.</p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-<?= $notificacao['tipo'] ?? 'danger' ?>"><?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?></div>
                        <?php endif; ?>

                        <form action="../controller/validar_token_action.php" method="POST">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">

                            <div class="form-floating mb-3">
                                <input type="text" id="token" name="token" class="form-control" required maxlength="6" pattern="\d{6}" placeholder="000000">
                                <label for="token"><i class="fas fa-lock me-2"></i>Código de 6 dígitos</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Validar Código
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="recuperar_senha.php" class="text-primary fw-bold"><i class="fas fa-arrow-left me-2"></i>Tentar novamente</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</main>
</body>
</html>
