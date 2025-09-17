<?php
require_once '../controller/iniciar.php';

// Protege a página
$seguranca->proteger_pagina('medico');

// Busca os dados atuais do médico para preencher o formulário
$medico_id = $_SESSION['usuario_id'];
$stmt = $conexaoBD->proteger_sql("SELECT * FROM user_medicos WHERE id = ?", [$medico_id]);
$dadosMedico = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dadosMedico) {
    // A linha abaixo interrompe o script e exibe uma página de erro amigável.
    // Usamos a sintaxe HEREDOC (<<<HTML) para escrever o bloco HTML de forma limpa.
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro na Consulta</title>
    <style>
        /* Estilo geral da página */
        body, html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        /* Container da mensagem de erro */
        .error-container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 90%;
            border-top: 5px solid #d9534f; /* Borda vermelha no topo para indicar erro */
        }

        /* Ícone de erro (SVG) */
        .error-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        /* Título do erro */
        .error-container h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #d9534f; /* Cor vermelha de erro */
        }

        /* Parágrafo com a mensagem */
        .error-container p {
            margin: 0 0 25px 0;
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        /* Botão para voltar */
        .back-button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff; /* Azul padrão */
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3; /* Azul mais escuro no hover */
        }
    </style>
</head>
<body>

    <div class="error-container">
        <svg class="error-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#d9534f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h1>Ocorreu um Erro</h1>
        <p>Não foi possível encontrar os dados do médico solicitado. Por favor, verifique as informações e tente novamente.</p>
        <a href="../publics/login.php)" class="back-button">Voltar</a>
    </div>

</body>
</html>
HTML;

    // A função exit() é importante para garantir que o resto do script não seja executado,
    // assim como o die() fazia.
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
    <title>Configurações do Perfil - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --medical-blue: #2c5aa0;
            --medical-light-blue: #4a7bc8;
            --medical-teal: #17a2b8;
            --medical-green: #28a745;
            --medical-gray: #6c757d;
            --medical-purple: #6f42c1;
            --medical-orange: #fd7e14;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--medical-blue) 0%, var(--medical-light-blue) 100%);
            color: white;
            min-height: 100vh;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .logo i {
            font-size: 2rem;
            color: #fff;
        }

        .logo h3 {
            color: #fff;
            font-weight: 600;
            margin: 0;
        }

        .nav-section {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin-bottom: 0.25rem;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #fff;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-left-color: #fff;
        }

        .nav-link i {
            width: 20px;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .logout-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: #fff;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: calc(100% - 3rem);
            margin: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            color: #fff;
        }

        .content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .header-section {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--medical-purple), var(--medical-teal), var(--medical-blue));
        }

        .page-title {
            color: var(--medical-blue);
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-subtitle {
            color: var(--medical-gray);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .config-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .config-header {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            color: #fff;
            padding: 1.5rem 2rem;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .config-body {
            padding: 2.5rem;
        }

        .profile-photo-section {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .profile-photo-title {
            color: var(--medical-blue);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .profile-img-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(44, 90, 160, 0.2);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(44, 90, 160, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .profile-img-container:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay i {
            font-size: 2rem;
            color: #fff;
        }

        .form-section {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid #e9ecef;
        }

        .form-label {
            font-weight: 600;
            color: var(--medical-blue);
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 90, 160, 0.25);
            background: #fff;
        }

        .form-control[readonly] {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-color: #dee2e6;
            color: var(--medical-gray);
        }

        .readonly-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medical-gray);
            opacity: 0.5;
        }

        .form-group {
            position: relative;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--medical-green), #20c997);
            border: none;
            color: #fff;
            padding: 1rem 3rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: #fff;
        }

        .btn-save:active {
            transform: translateY(-1px);
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .alert-success-custom {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            color: var(--medical-green);
            border-left: 4px solid var(--medical-green);
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 118, 117, 0.1));
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-badge.online {
            background: rgba(40, 167, 69, 0.1);
            color: var(--medical-green);
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-custom {
            background: linear-gradient(135deg, var(--medical-teal), #17a2b8);
            border: none;
            color: #fff;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }

        .file-input-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row.single {
            grid-template-columns: 1fr;
        }

        .save-section {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 1rem; }
            .form-row { grid-template-columns: 1fr; }
            .profile-img { width: 120px; height: 120px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>
        </div>

        <div class="nav-section">
            <a href="dashboard_medico.php" class="nav-link">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>
            <a href="consulta.php" class="nav-link">
                <i class="fas fa-user-md"></i>
                Nova Consulta
            </a>
            <a href="prontuario.php" class="nav-link">
                <i class="fas fa-notes-medical"></i>
                Prontuários
            </a>
            <a href="configuracao_medico.php" class="nav-link active">
                <i class="fas fa-user-cog"></i>
                Configurações
            </a>
        </div>

        <form action="../controller/logout.php" method="post">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Encerrar Sessão
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-user-cog"></i>
                Configurações do Perfil
            </h1>
            <p class="page-subtitle">Gerencie suas informações pessoais e profissionais. Mantenha seus dados sempre atualizados para um atendimento mais eficiente.</p>
            
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge online">
                    <i class="fas fa-shield-alt"></i>
                    Dados Seguros LGPD
                </span>
                <span class="text-muted">
                    <i class="fas fa-save me-1"></i>
                    Salvamento automático
                </span>
            </div>
        </div>

        <!-- Notifications -->
        <?php if ($notificacao): ?>
            <div class="alert <?= $notificacao['tipo'] === 'sucesso' ? 'alert-success-custom' : 'alert-danger-custom' ?> alert-custom alert-dismissible fade show" role="alert">
                <i class="fas <?= $notificacao['tipo'] === 'sucesso' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Configuration Form -->
        <form action="../controller/Valida_configuracao_medico.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
            
            <div class="config-card">
                <div class="config-header">
                    <i class="fas fa-user-edit"></i>
                    Informações do Perfil
                </div>
                <div class="config-body">
                    <!-- Profile Photo Section -->
                    <div class="profile-photo-section">
                        <h5 class="profile-photo-title">
                            <i class="fas fa-camera me-2"></i>
                            Foto de Perfil
                        </h5>
                        <div class="profile-img-container">
                            <img src="../uploads/fotos_perfil/<?= $seguranca->sanitizar_entrada($dadosMedico['foto_perfil'] ?: 'default.png') ?>" 
                                 alt="Foto de Perfil" 
                                 class="profile-img"
                                 id="preview-img">
                            <div class="photo-overlay" onclick="document.getElementById('foto_perfil').click()">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <div class="file-input-wrapper">
                            <button type="button" class="file-input-custom" onclick="document.getElementById('foto_perfil').click()">
                                <i class="fas fa-upload me-2"></i>
                                Alterar Foto de Perfil
                            </button>
                            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                        </div>
                    </div>

                    <!-- Form Fields Section -->
                    <div class="form-section">
                        <!-- Nome and CRM Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Nome Completo
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $seguranca->sanitizar_entrada($dadosMedico['nome']) ?>" readonly>
                                <i class="fas fa-lock readonly-icon"></i>
                            </div>
                            <div class="form-group">
                                <label for="crm" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>
                                    CRM
                                </label>
                                <input type="text" class="form-control" id="crm" name="crm" value="<?= $seguranca->sanitizar_entrada($dadosMedico['crm']) ?>" readonly>
                                <i class="fas fa-lock readonly-icon"></i>
                            </div>
                        </div>

                        <!-- Email and Phone Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    E-mail
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $seguranca->sanitizar_entrada($dadosMedico['email']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="telefone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>
                                    Telefone
                                </label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $seguranca->sanitizar_entrada($dadosMedico['telefone']) ?>">
                            </div>
                        </div>

                        <!-- Address Row -->
                        <div class="form-row single">
                            <div class="form-group">
                                <label for="endereco" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Endereço Completo
                                </label>
                                <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $seguranca->sanitizar_entrada($dadosMedico['endereco']) ?>">
                            </div>
                        </div>

                        <!-- City and State Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cidade" class="form-label">
                                    <i class="fas fa-city me-2"></i>
                                    Cidade
                                </label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $seguranca->sanitizar_entrada($dadosMedico['cidade']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-flag me-2"></i>
                                    Estado
                                </label>
                                <input type="text" class="form-control" id="estado" name="estado" value="<?= $seguranca->sanitizar_entrada($dadosMedico['estado']) ?>">
                            </div>
                        </div>

                        <!-- Birth Date and Status Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data_nascimento" class="form-label">
                                    <i class="fas fa-birthday-cake me-2"></i>
                                    Data de Nascimento
                                </label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $seguranca->sanitizar_entrada($dadosMedico['data_nascimento']) ?>" readonly>
                                <i class="fas fa-lock readonly-icon"></i>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    Status Profissional
                                </label>
                                <select class="form-select" name="status_atual">
                                    <option value="Ativo" <?= ($dadosMedico['status_atual'] === 'Ativo') ? 'selected' : '' ?>>
                                        <i class="fas fa-check-circle"></i> Ativo
                                    </option>
                                    <option value="Inativo" <?= ($dadosMedico['status_atual'] === 'Inativo') ? 'selected' : '' ?>>
                                        <i class="fas fa-times-circle"></i> Inativo
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Save Section -->
                    <div class="save-section">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>
                            Salvar Alterações
                        </button>
                        <p class="text-muted mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Suas informações serão atualizadas com segurança
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animações de entrada
            const animateElements = document.querySelectorAll('.header-section, .config-card');
            animateElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Preview da foto
            const fotoInput = document.getElementById('foto_perfil');
            const previewImg = document.getElementById('preview-img');

            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        // Adicionar animação de mudança
                        previewImg.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            previewImg.style.transition = 'transform 0.3s ease';
                            previewImg.style.transform = 'scale(1)';
                        }, 100);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Máscaras para campos
            const telefoneInput = document.getElementById('telefone');
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            });

            // Validação do formulário
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const telefone = document.getElementById('telefone').value;

                if (!email || !email.includes('@')) {
                    e.preventDefault();
                    alert('Por favor, insira um e-mail válido.');
                    return;
                }

                if (telefone && telefone.length < 14) {
                    e.preventDefault();
                    alert('Por favor, insira um telefone válido.');
                    return;
                }

                // Animação do botão de submit
                const submitBtn = form.querySelector('.btn-save');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
                submitBtn.disabled = true;
            });

            // Efeitos hover nos campos readonly
            const readonlyFields = document.querySelectorAll('input[readonly]');
            readonlyFields.forEach(field => {
                field.addEventListener('mouseenter', function() {
                    this.style.cursor = 'not-allowed';
                });
            });
        });
    </script>
</body>
</html>