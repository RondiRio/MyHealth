<?php
require_once 'iniciar.php';

// Protege a página para garantir que apenas pacientes logados possam acessá-la.
$seguranca->proteger_pagina('paciente');

// Busca todos os dados do paciente que está logado na sessão.
$paciente_id = $_SESSION['usuario_id'];
$stmt = $conexaoBD->proteger_sql("SELECT * FROM user_pacientes WHERE id = ?", [$paciente_id]);
$dadosPaciente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dadosPaciente) {
    die("Erro: Não foi possível carregar os dados do paciente.");
}

// Prepara o sistema de notificação para feedback (ex: "Perfil atualizado!").
$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Paciente - MediCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style_configura_paciente.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mb-0">Salvando alterações...</p>
        </div>
    </div>

    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-cog"></i>
                        Configurações do Perfil
                    </h1>
                    <p class="page-subtitle">Gerencie suas informações pessoais e configurações de conta</p>
                </div>
                <a href="dashBoard_Paciente.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao Dashboard
                </a>
            </div>
        </div>

        <!-- Notifications -->
        <?php if ($notificacao): ?>
            <div class="alert alert-<?= $notificacao['tipo'] === 'sucesso' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $notificacao['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Main Form -->
        <form action="processa_configuracao_paciente.php" method="POST" enctype="multipart/form-data" id="configForm">
            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
            
            <div class="card main-card">
                <div class="card-body">
                    <div class="row g-4">
                        
                        <!-- Profile Photo Section -->
                        <div class="col-lg-4">
                            <div class="profile-section">
                                <h5 class="profile-title">
                                    <i class="fas fa-camera"></i>
                                    Foto de Perfil
                                </h5>
                                
                                <div class="patient-info-badge">
                                    <i class="fas fa-id-badge"></i>
                                    Paciente #<?= str_pad($dadosPaciente['id'], 5, '0', STR_PAD_LEFT) ?>
                                </div>
                                
                                <img src="uploads/fotos_perfil/<?= htmlspecialchars($dadosPaciente['foto_perfil'] ?: 'default-paciente.png') ?>" 
                                     alt="Foto de Perfil" 
                                     class="profile-img" 
                                     id="profilePreview">
                                
                                <div class="file-upload-area">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <label for="foto_perfil" class="form-label mb-2">
                                        <i class="fas fa-image"></i>
                                        Alterar Foto
                                    </label>
                                    <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*">
                                    <small class="text-muted mt-2 d-block">Formatos aceitos: JPG, PNG, GIF (máx. 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Section -->
                        <div class="col-lg-8">
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-user-edit"></i>
                                    Informações Pessoais
                                </h5>
                                
                                <div class="row g-3">
                                    <!-- Nome Completo -->
                                    <div class="col-12">
                                        <label for="nome_paciente" class="form-label">
                                            <i class="fas fa-user"></i>
                                            Nome Completo
                                            <span class="readonly-badge">
                                                <i class="fas fa-lock"></i>
                                                Somente Leitura
                                            </span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control with-icon" id="nome_paciente" name="nome_paciente" value="<?= htmlspecialchars($dadosPaciente['nome_paciente']) ?>" readonly>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            O nome completo não pode ser alterado por questões de segurança.
                                        </div>
                                    </div>

                                    <!-- CPF -->
                                    <div class="col-md-6">
                                        <label for="cpf" class="form-label">
                                            <i class="fas fa-id-card"></i>
                                            CPF
                                            <span class="readonly-badge">
                                                <i class="fas fa-lock"></i>
                                                Somente Leitura
                                            </span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                            <input type="text" class="form-control with-icon" id="cpf" name="cpf" value="<?= htmlspecialchars($dadosPaciente['cpf']) ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i>
                                            E-mail
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control with-icon" id="email" name="email" value="<?= htmlspecialchars($dadosPaciente['email']) ?>">
                                        </div>
                                    </div>

                                    <!-- Telefone -->
                                    <div class="col-md-6">
                                        <label for="telefone" class="form-label">
                                            <i class="fas fa-phone"></i>
                                            Telefone
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="text" class="form-control with-icon" id="telefone" name="telefone" value="<?= htmlspecialchars($dadosPaciente['telefone']) ?>" maxlength="15" pattern="\(\d{2}\)\d{5}-\d{4}" placeholder="(00)91234-5678" oninput="mascaraTelefone(this)">
                                        </div>
                                    </div>

                                    <!-- Data de Nascimento -->
                                    <div class="col-md-6">
                                        <label for="data_nascimento" class="form-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            Data de Nascimento
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" class="form-control with-icon" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($dadosPaciente['data_nascimento']) ?>">
                                        </div>
                                    </div>

                                    <!-- Endereço -->
                                    <div class="col-12">
                                        <label for="endereco" class="form-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Endereço Completo
                                        </label>
                                        <div class="input-group">
                                            <span class="input-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text" class="form-control with-icon" id="endereco" name="endereco" value="<?= htmlspecialchars($dadosPaciente['endereco']) ?>" placeholder="Rua, número, bairro, cidade - UF">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="text-end mt-4 pt-3 border-top">
                       <button type="submit" class="btn-save" id="saveBtn">
                           <i class="fas fa-save"></i>
                           Salvar Todas as Alterações
                       </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Máscara de telefone
        function mascaraTelefone(campo) {
            let v = campo.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 6) {
                campo.value = `(${v.slice(0,2)})${v.slice(2,7)}-${v.slice(7)}`;
            } else if (v.length > 2) {
                campo.value = `(${v.slice(0,2)})${v.slice(2)}`;
            } else if (v.length > 0) {
                campo.value = `(${v}`;
            }
        }

        // Preview da imagem
        document.getElementById('foto_perfil').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Loading overlay no submit
        document.getElementById('configForm').addEventListener('submit', function(e) {
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('saveBtn').disabled = true;
        });

        // Validação do formulário
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('configForm');
            const inputs = form.querySelectorAll('input[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // Animação nos campos de input
            const formControls = document.querySelectorAll('.form-control:not([readonly])');
            
            formControls.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentNode.style.transform = 'scale(1.02)';
                    this.parentNode.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentNode.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>