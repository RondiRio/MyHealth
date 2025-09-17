<?php
require_once '../controller/iniciar.php';
$seguranca->proteger_pagina('medico');

// Busca os dados do médico logado para exibir no cabeçalho
$stmt = $conexaoBD->proteger_sql("SELECT id, nome, crm, foto_perfil, especialidade FROM user_medicos WHERE id = ?", [$_SESSION['usuario_id']]);
$medico = $stmt->get_result()->fetch_assoc();
$stmt->close();

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nova Consulta - MyHealth</title>
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
            --danger-red: #dc3545;
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
            position: fixed;
            z-index: 1000;
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

        .doctor-profile {
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.2);
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .profile-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .doctor-crm {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 0.25rem;
        }

        .doctor-specialty {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
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
            background: linear-gradient(45deg, var(--danger-red), #c82333);
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
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header-section {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin: 2rem;
            margin-bottom: 1rem;
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

        .main-content {
            flex: 1;
            padding: 0 2rem 2rem 2rem;
            overflow-y: auto;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--medical-blue), var(--medical-light-blue));
        }

        .card-header {
            background: transparent;
            border-bottom: 2px solid #e9ecef;
            padding: 0;
            position: relative;
        }

        .nav-tabs {
            border-bottom: none;
            padding: 0;
            background: none;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--medical-gray);
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 10px 10px 0 0;
            margin-right: 0.5rem;
            background: transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(44, 90, 160, 0.1);
            color: var(--medical-blue);
        }

        .nav-tabs .nav-link.active {
            background: var(--medical-blue);
            color: #fff;
            border-bottom-color: var(--medical-blue);
        }

        .tab-content {
            background: #fff;
        }

        .tab-pane {
            padding: 2rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 90, 160, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--medical-blue);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 1rem 2rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 90, 160, 0.3);
            color: #fff;
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--medical-gray), #5a6268);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 1rem 2rem;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
            color: #fff;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--medical-green), #218838);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
            color: #fff;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            color: var(--medical-green);
            border-left: 4px solid var(--medical-green);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
            color: var(--danger-red);
            border-left: 4px solid var(--danger-red);
        }

        .text-danger {
            color: var(--danger-red) !important;
        }

        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .vital-sign-card {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .vital-sign-card:hover {
            border-color: var(--medical-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(44, 90, 160, 0.1);
        }

        .vital-sign-icon {
            font-size: 2rem;
            color: var(--medical-teal);
            margin-bottom: 0.5rem;
        }

        .vital-sign-label {
            font-weight: 600;
            color: var(--medical-blue);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .patient-search-container {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 2px solid #e9ecef;
        }

        .patient-info-display {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .patient-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .patient-details {
            font-size: 1rem;
            opacity: 0.9;
        }

        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--medical-green);
            border-color: var(--medical-green);
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--medical-blue), transparent);
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            body { 
                flex-direction: column; 
            }
            .sidebar { 
                width: 100%; 
                min-height: auto; 
                position: relative;
            }
            .content { 
                margin-left: 0;
            }
            .header-section,
            .main-content {
                margin-left: 1rem;
                margin-right: 1rem;
            }
            .nav-tabs .nav-link { 
                padding: 1rem 0.5rem;
                font-size: 0.9rem;
            }
            .vital-signs-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
            <h3>MyHealth</h3>
        </div>
        <div class="doctor-profile">
            <img src="../uploads/fotos_perfil/<?= htmlspecialchars($medico['foto_perfil'] ?: 'default-medico.png') ?>" class="profile-img" alt="Foto de perfil">
            <div class="profile-name">Dr. <?= htmlspecialchars($medico['nome']); ?></div>
            <div class="doctor-crm">CRM: <?= htmlspecialchars($medico['crm']); ?></div>
            <?php if (!empty($medico['especialidade'])): ?>
                <div class="doctor-specialty"><?= htmlspecialchars($medico['especialidade']); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="nav-section">
        <a href="dashboard_medico.php" class="nav-link">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>
        <a href="#" class="nav-link active">
            <i class="fas fa-user-md"></i>
            Nova Consulta
        </a>
        <a href="prontuario.php" class="nav-link">
            <i class="fas fa-notes-medical"></i>
            Prontuários
        </a>
        <a href="configuracao_medico.php" class="nav-link">
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

<div class="content">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="page-title">
            <i class="fas fa-user-md"></i>
            Nova Consulta Médica
        </h1>
        <p class="page-subtitle">Registre uma nova consulta no prontuário eletrônico do paciente com dados completos e seguros.</p>
        
        <div class="d-flex align-items-center gap-3">
            <span class="status-badge online">
                <i class="fas fa-shield-alt"></i>
                Sistema Seguro
            </span>
            <span class="text-muted">
                <i class="fas fa-clock me-1"></i>
                Sessão ativa
            </span>
        </div>
    </div>

    <div class="main-content">
        <?php if ($notificacao): ?>
            <div class="alert alert-<?= $notificacao['tipo'] === 'sucesso' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $notificacao['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="../controller/registrar_consulta.php" method="POST" enctype="multipart/form-data" id="consultaForm">
            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
            
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="consultaTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="identificacao-tab" data-bs-toggle="tab" data-bs-target="#identificacao" type="button">
                                <i class="fas fa-user me-2"></i>
                                Identificação
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="anamnese-tab" data-bs-toggle="tab" data-bs-target="#anamnese" type="button">
                                <i class="fas fa-comments me-2"></i>
                                Anamnese
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sinais-vitais-tab" data-bs-toggle="tab" data-bs-target="#sinais-vitais" type="button">
                                <i class="fas fa-heartbeat me-2"></i>
                                Sinais Vitais
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="exame-tab" data-bs-toggle="tab" data-bs-target="#exame" type="button">
                                <i class="fas fa-stethoscope me-2"></i>
                                Exame Físico
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="diagnostico-tab" data-bs-toggle="tab" data-bs-target="#diagnostico" type="button">
                                <i class="fas fa-diagnoses me-2"></i>
                                Diagnóstico
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="conduta-tab" data-bs-toggle="tab" data-bs-target="#conduta" type="button">
                                <i class="fas fa-prescription-bottle-alt me-2"></i>
                                Conduta
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="anexos-tab" data-bs-toggle="tab" data-bs-target="#anexos" type="button">
                                <i class="fas fa-paperclip me-2"></i>
                                Anexos
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content" id="consultaTabContent">
                    <!-- ABA 1: IDENTIFICAÇÃO -->
                    <div class="tab-pane fade show active" id="identificacao" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-id-card me-2"></i>
                            Dados do Paciente e da Consulta
                        </h5>
                        
                        <!-- Busca do Paciente -->
                        <div class="patient-search-container">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="cpf_paciente" class="form-label">
                                        <i class="fas fa-id-card me-1"></i>
                                        CPF do Paciente <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" id="cpf_paciente" name="cpf_paciente" class="form-control" placeholder="000.000.000-00" required>
                                        <button class="btn btn-primary" type="button" id="buscar_paciente">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="data_consulta" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Data e Hora da Consulta <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" id="data_consulta" name="data_consulta" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Informações do Paciente (aparecem após busca) -->
                        <div id="patient_info" class="patient-info-display d-none">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="patient-name" id="patient_name"></div>
                                    <div class="patient-details" id="patient_details"></div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-light btn-sm" onclick="clearPatientSearch()">
                                        <i class="fas fa-times me-1"></i>
                                        Buscar Outro
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Adicionais -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome_paciente" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Nome Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="nome_paciente" name="nome_paciente" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="data_nascimento" class="form-label">
                                    <i class="fas fa-birthday-cake me-1"></i>
                                    Data de Nascimento
                                </label>
                                <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="genero" class="form-label">
                                    <i class="fas fa-venus-mars me-1"></i>
                                    Gênero
                                </label>
                                <select id="genero" name="genero" class="form-select">
                                    <option value="">Selecionar</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                    <option value="Outro">Outro</option>
                                    <option value="Prefiro não informar">Prefiro não informar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="convenio" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Convênio / Plano de Saúde
                                </label>
                                <input type="text" id="convenio" name="convenio" class="form-control" placeholder="Nome do convênio ou Particular">
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_consulta" class="form-label">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    Tipo de Consulta
                                </label>
                                <select id="tipo_consulta" name="tipo_consulta" class="form-select">
                                    <option value="Consulta de Rotina">Consulta de Rotina</option>
                                    <option value="Consulta de Urgência">Consulta de Urgência</option>
                                    <option value="Retorno">Retorno</option>
                                    <option value="Check-up">Check-up</option>
                                    <option value="Consulta Especializada">Consulta Especializada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ABA 2: ANAMNESE -->
                    <div class="tab-pane fade" id="anamnese" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-comments me-2"></i>
                            Histórico Clínico e Anamnese
                        </h5>
                        
                        <div class="mb-4">
                            <label for="queixa_principal" class="form-label">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Queixa Principal (QP) e História da Doença Atual (HDA)
                            </label>
                            <textarea id="queixa_principal" name="anamnese" class="form-control" rows="5" placeholder="Descreva os sintomas atuais, tempo de evolução, fatores que melhoram ou pioram..."></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="historico_patologico" class="form-label">
                                <i class="fas fa-history me-1"></i>
                                Histórico Patológico Pregresso (HPP)
                            </label>
                            <textarea id="historico_patologico" name="historico_patologico" class="form-control" rows="4" placeholder="Doenças anteriores, cirurgias, internações, traumatismos..."></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="historico_familiar" class="form-label">
                                    <i class="fas fa-users me-1"></i>
                                    Histórico Familiar
                                </label>
                                <textarea id="historico_familiar" name="historico_familiar" class="form-control" rows="3" placeholder="Doenças na família (pais, irmãos, avós)..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="alergias_medicamentos" class="form-label">
                                    <i class="fas fa-pills me-1"></i>
                                    Alergias e Medicamentos em Uso
                                </label>
                                <textarea id="alergias_medicamentos" name="alergias_medicamentos" class="form-control" rows="3" placeholder="Alergias conhecidas, medicamentos em uso contínuo..."></textarea>
                            </div>
                        </div>
                        
                        <hr class="section-divider">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="habitos_vida" class="form-label">
                                    <i class="fas fa-smoking me-1"></i>
                                    Hábitos de Vida
                                </label>
                                <textarea id="habitos_vida" name="habitos_vida" class="form-control" rows="3" placeholder="Tabagismo, etilismo, atividade física, alimentação..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="revisao_sistemas" class="form-label">
                                    <i class="fas fa-list-check me-1"></i>
                                    Revisão de Sistemas
                                </label>
                                <textarea id="revisao_sistemas" name="revisao_sistemas" class="form-control" rows="3" placeholder="Sintomas por sistemas (cardiovascular, respiratório, digestivo...)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ABA 3: SINAIS VITAIS -->
                    <div class="tab-pane fade" id="sinais-vitais" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-heartbeat me-2"></i>
                            Sinais Vitais e Dados Antropométricos
                        </h5>
                        
                        <div class="vital-signs-grid">
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div class="vital-sign-label">Pressão Arterial</div>
                                <input type="text" name="sinais_vitais[pa]" class="form-control" placeholder="120/80 mmHg">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="vital-sign-label">Frequência Cardíaca</div>
                                <input type="text" name="sinais_vitais[fc]" class="form-control" placeholder="72 bpm">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-lungs"></i>
                                </div>
                                <div class="vital-sign-label">Frequência Respiratória</div>
                                <input type="text" name="sinais_vitais[fr]" class="form-control" placeholder="16 irpm">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <div class="vital-sign-label">Temperatura</div>
                                <input type="text" name="sinais_vitais[temp]" class="form-control" placeholder="36.5°C">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="vital-sign-label">Saturação O₂</div>
                                <input type="text" name="sinais_vitais[spo2]" class="form-control" placeholder="98%">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <div class="vital-sign-label">Peso</div>
                                <input type="text" name="sinais_vitais[peso]" class="form-control" placeholder="70 kg">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-ruler-vertical"></i>
                                </div>
                                <div class="vital-sign-label">Altura</div>
                                <input type="text" name="sinais_vitais[altura]" class="form-control" placeholder="170 cm">
                            </div>
                            
                            <div class="vital-sign-card">
                                <div class="vital-sign-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="vital-sign-label">IMC</div>
                                <input type="text" name="sinais_vitais[imc]" class="form-control" placeholder="24.2 kg/m²" readonly id="imc_calculado">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="outros_dados_clinicos" class="form-label">
                                <i class="fas fa-notes-medical me-1"></i>
                                Outros Dados Clínicos
                            </label>
                            <textarea id="outros_dados_clinicos" name="outros_dados_clinicos" class="form-control" rows="3" placeholder="HGT, dor (EVA), Glasgow, outros parâmetros específicos..."></textarea>
                        </div>
                    </div>

                    <!-- ABA 4: EXAME FÍSICO -->
                    <div class="tab-pane fade" id="exame" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-stethoscope me-2"></i>
                            Exame Físico Detalhado
                        </h5>
                        
                        <div class="mb-4">
                            <label for="estado_geral" class="form-label">
                                <i class="fas fa-user-check me-1"></i>
                                Estado Geral e Fácies
                            </label>
                            <textarea id="estado_geral" name="estado_geral" class="form-control" rows="2" placeholder="BEG, corado, hidratado, anictérico, acianótico, afebril..."></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cabeca_pescoco" class="form-label">
                                    <i class="fas fa-head-side-mask me-1"></i>
                                    Cabeça e Pescoço
                                </label>
                                <textarea id="cabeca_pescoco" name="cabeca_pescoco" class="form-control" rows="3" placeholder="Orofaringe, linfonodos, tireoide, jugulares..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="aparelho_cardiovascular" class="form-label">
                                    <i class="fas fa-heart me-1"></i>
                                    Aparelho Cardiovascular
                                </label>
                                <textarea id="aparelho_cardiovascular" name="aparelho_cardiovascular" class="form-control" rows="3" placeholder="Precórdio, bulhas, sopros, ritmo, pulsos periféricos..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="aparelho_respiratorio" class="form-label">
                                    <i class="fas fa-lungs me-1"></i>
                                    Aparelho Respiratório
                                </label>
                                <textarea id="aparelho_respiratorio" name="aparelho_respiratorio" class="form-control" rows="3" placeholder="Inspeção, palpação, percussão, ausculta pulmonar..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="abdome" class="form-label">
                                    <i class="fas fa-prescription me-1"></i>
                                    Abdome
                                </label>
                                <textarea id="abdome" name="abdome" class="form-control" rows="3" placeholder="Inspeção, ausculta, palpação, percussão, massas, visceromegalias..."></textarea>
                            </div>
                        </div>
                        
                        <hr class="section-divider">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="extremidades" class="form-label">
                                    <i class="fas fa-hands me-1"></i>
                                    Extremidades
                                </label>
                                <textarea id="extremidades" name="extremidades" class="form-control" rows="3" placeholder="Edema, pulsos, perfusão, temperatura, mobilidade..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="sistema_neurologico" class="form-label">
                                    <i class="fas fa-brain me-1"></i>
                                    Sistema Neurológico
                                </label>
                                <textarea id="sistema_neurologico" name="sistema_neurologico" class="form-control" rows="3" placeholder="Consciência, orientação, reflexos, força, sensibilidade..."></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="exame_fisico_completo" class="form-label">
                                <i class="fas fa-clipboard-check me-1"></i>
                                Resumo do Exame Físico / Outros Achados
                            </label>
                            <textarea id="exame_fisico_completo" name="exame_fisico" class="form-control" rows="4" placeholder="Descrição geral dos achados do exame físico..."></textarea>
                        </div>
                    </div>

                    <!-- ABA 5: DIAGNÓSTICO -->
                    <div class="tab-pane fade" id="diagnostico" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-diagnoses me-2"></i>
                            Avaliação e Diagnóstico
                        </h5>
                        
                        <div class="mb-4">
                            <label for="hipotese_diagnostica" class="form-label">
                                <i class="fas fa-question-circle me-1"></i>
                                Hipóteses Diagnósticas
                            </label>
                            <textarea id="hipotese_diagnostica" name="hipotese_diagnostica" class="form-control" rows="3" placeholder="Liste as principais hipóteses diagnósticas em ordem de probabilidade..."></textarea>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="diagnostico_final" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Diagnóstico Principal
                                </label>
                                <input type="text" id="diagnostico_final" name="diagnostico_final" class="form-control" placeholder="Diagnóstico principal com CID-10">
                            </div>
                            <div class="col-md-4">
                                <label for="cid_10" class="form-label">
                                    <i class="fas fa-code me-1"></i>
                                    Código CID-10
                                </label>
                                <input type="text" id="cid_10" name="cid_10" class="form-control" placeholder="Ex: J06.9">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="diagnosticos_secundarios" class="form-label">
                                <i class="fas fa-list me-1"></i>
                                Diagnósticos Secundários / Comorbidades
                            </label>
                            <textarea id="diagnosticos_secundarios" name="diagnosticos_secundarios" class="form-control" rows="3" placeholder="Outros diagnósticos relevantes, comorbidades..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="exames_solicitados" class="form-label">
                                <i class="fas fa-vials me-1"></i>
                                Exames Complementares Solicitados
                            </label>
                            <textarea id="exames_solicitados" name="exames_solicitados" class="form-control" rows="4" placeholder="Exames laboratoriais, de imagem, outros procedimentos diagnósticos..."></textarea>
                        </div>
                    </div>

                    <!-- ABA 6: CONDUTA -->
                    <div class="tab-pane fade" id="conduta" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-prescription-bottle-alt me-2"></i>
                            Conduta Terapêutica
                        </h5>
                        
                        <div class="mb-4">
                            <label for="tratamento_proposto" class="form-label">
                                <i class="fas fa-pills me-1"></i>
                                Prescrição Médica / Tratamento
                            </label>
                            <textarea id="tratamento_proposto" name="tratamento_proposto" class="form-control" rows="6" placeholder="Medicamentos (nome, dosagem, via, frequência, duração), orientações terapêuticas..."></textarea>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="orientacoes_paciente" class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Orientações ao Paciente
                                </label>
                                <textarea id="orientacoes_paciente" name="orientacoes_paciente" class="form-control" rows="4" placeholder="Cuidados domiciliares, sinais de alerta, quando retornar..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="encaminhamentos" class="form-label">
                                    <i class="fas fa-share me-1"></i>
                                    Encaminhamentos
                                </label>
                                <textarea id="encaminhamentos" name="encaminhamentos" class="form-control" rows="4" placeholder="Especialistas, fisioterapia, outros profissionais..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="data_retorno" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Data de Retorno Sugerida
                                </label>
                                <input type="date" id="data_retorno" name="data_retorno" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="prognóstico" class="form-label">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Prognóstico
                                </label>
                                <select id="prognostico" name="prognostico" class="form-select">
                                    <option value="">Selecionar</option>
                                    <option value="Excelente">Excelente</option>
                                    <option value="Bom">Bom</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Reservado">Reservado</option>
                                    <option value="Grave">Grave</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observacoes_privadas" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                <strong>Anotações Privadas do Médico</strong> (Não visível ao paciente)
                            </label>
                            <textarea id="observacoes_privadas" name="observacoes_privadas" class="form-control" rows="3" placeholder="Observações confidenciais, impressões clínicas, notas para próxima consulta..."></textarea>
                        </div>
                    </div>

                    <!-- ABA 7: ANEXOS -->
                    <div class="tab-pane fade" id="anexos" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-paperclip me-2"></i>
                            Documentos e Anexos
                        </h5>
                        
                        <div class="mb-4">
                            <label for="documentos" class="form-label">
                                <i class="fas fa-upload me-1"></i>
                                Anexar Arquivos
                            </label>
                            <input class="form-control form-control-lg" type="file" id="documentos" name="documentos[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tipos aceitos: JPG, PNG, PDF, DOC, DOCX. Tamanho máximo: 10MB por arquivo.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="descricao_anexos" class="form-label">
                                <i class="fas fa-comment-alt me-1"></i>
                                Descrição dos Anexos
                            </label>
                            <textarea id="descricao_anexos" name="descricao_anexos" class="form-control" rows="3" placeholder="Descreva os arquivos anexados (exames, laudos, imagens, etc.)"></textarea>
                        </div>
                        
                        <hr class="section-divider">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" role="switch" id="visivel_para_paciente" name="visivel_para_paciente" value="1" checked>
                                    <label class="form-check-label" for="visivel_para_paciente">
                                        <strong>Tornar consulta visível para o paciente</strong>
                                        <br><small class="text-muted">O paciente poderá visualizar este registro em seu portal</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" role="switch" id="consulta_urgencia" name="consulta_urgencia" value="1">
                                    <label class="form-check-label" for="consulta_urgencia">
                                        <strong>Marcar como consulta de urgência</strong>
                                        <br><small class="text-muted">Identificará este atendimento como prioritário</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <button type="button" class="btn btn-secondary w-100" onclick="salvarRascunho()">
                        <i class="fas fa-save me-2"></i>
                        Salvar Rascunho
                    </button>
                </div>
                <div class="col-md-4">
                    <a href="dashboard_medico.php" class="btn btn-secondary w-100">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100 h-100">
                        <i class="fas fa-check-circle me-2"></i>
                        Finalizar Consulta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Máscaras de entrada
        IMask(document.getElementById('cpf_paciente'), { mask: '000.000.000-00' });
        
        // Definir data/hora atual como padrão
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('data_consulta').value = now.toISOString().slice(0, 16);
        
        // Busca de paciente
        document.getElementById('buscar_paciente').addEventListener('click', function() {
            buscarPaciente();
        });
        
        // Busca automática ao sair do campo CPF
        document.getElementById('cpf_paciente').addEventListener('blur', function() {
            if (this.value.length === 14) {
                buscarPaciente();
            }
        });
        
        // Cálculo automático do IMC
        const pesoInput = document.querySelector('input[name="sinais_vitais[peso]"]');
        const alturaInput = document.querySelector('input[name="sinais_vitais[altura]"]');
        const imcInput = document.getElementById('imc_calculado');
        
        function calcularIMC() {
            const peso = parseFloat(pesoInput.value);
            const altura = parseFloat(alturaInput.value) / 100; // converter cm para m
            
            if (peso && altura) {
                const imc = peso / (altura * altura);
                imcInput.value = imc.toFixed(1) + ' kg/m²';
            }
        }
        
        pesoInput.addEventListener('input', calcularIMC);
        alturaInput.addEventListener('input', calcularIMC);
        
        // Validação do formulário
        document.getElementById('consultaForm').addEventListener('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault();
            }
        });
        
        // Animações de entrada
        animatePageElements();
    });
    
    function buscarPaciente() {
        const cpf = document.getElementById('cpf_paciente').value;
        const button = document.getElementById('buscar_paciente');
        
        if (cpf.length !== 14) {
            alert('Por favor, digite um CPF válido.');
            return;
        }
        
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';
        
        fetch('../controller/buscar_paciente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                cpf: cpf
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.paciente) {
                preencherDadosPaciente(data.paciente);
                mostrarInfoPaciente(data.paciente);
            } else {
                // Paciente não encontrado - permitir cadastro
                document.getElementById('nome_paciente').focus();
                alert('Paciente não encontrado. Preencha os dados para cadastrar um novo paciente.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao buscar paciente. Tente novamente.');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-search"></i>';
        });
    }
    
    function preencherDadosPaciente(paciente) {
        document.getElementById('nome_paciente').value = paciente.nome || '';
        document.getElementById('data_nascimento').value = paciente.data_nascimento || '';
        document.getElementById('genero').value = paciente.genero || '';
        
        // Preencher outros campos se existirem no objeto paciente
        if (paciente.convenio) {
            document.getElementById('convenio').value = paciente.convenio;
        }
    }
    
    function mostrarInfoPaciente(paciente) {
        const patientInfo = document.getElementById('patient_info');
        const patientName = document.getElementById('patient_name');
        const patientDetails = document.getElementById('patient_details');
        
        patientName.textContent = paciente.nome;
        
        let detailsText = '';
        if (paciente.data_nascimento) {
            const idade = new Date().getFullYear() - new Date(paciente.data_nascimento).getFullYear();
            detailsText += `${idade} anos • `;
        }
        detailsText += `CPF: ${paciente.cpf}`;
        if (paciente.telefone) {
            detailsText += ` • ${paciente.telefone}`;
        }
        
        patientDetails.textContent = detailsText;
        patientInfo.classList.remove('d-none');
    }
    
    function clearPatientSearch() {
        document.getElementById('patient_info').classList.add('d-none');
        document.getElementById('cpf_paciente').value = '';
        document.getElementById('nome_paciente').value = '';
        document.getElementById('data_nascimento').value = '';
        document.getElementById('genero').value = '';
        document.getElementById('convenio').value = '';
    }
    
    function validarFormulario() {
        const cpf = document.getElementById('cpf_paciente').value;
        const nome = document.getElementById('nome_paciente').value;
        const dataConsulta = document.getElementById('data_consulta').value;
        
        if (!cpf || cpf.length !== 14) {
            alert('Por favor, digite um CPF válido.');
            document.getElementById('identificacao-tab').click();
            document.getElementById('cpf_paciente').focus();
            return false;
        }
        
        if (!nome.trim()) {
            alert('Por favor, digite o nome do paciente.');
            document.getElementById('identificacao-tab').click();
            document.getElementById('nome_paciente').focus();
            return false;
        }
        
        if (!dataConsulta) {
            alert('Por favor, selecione a data e hora da consulta.');
            document.getElementById('identificacao-tab').click();
            document.getElementById('data_consulta').focus();
            return false;
        }
        
        return true;
    }
    
    function salvarRascunho() {
        const formData = new FormData(document.getElementById('consultaForm'));
        formData.append('salvar_rascunho', '1');
        
        fetch('salvar_rascunho_consulta.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Rascunho salvo com sucesso!');
            } else {
                alert('Erro ao salvar rascunho: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar rascunho.');
        });
    }
    
    function animatePageElements() {
        const elements = document.querySelectorAll('.header-section, .card');
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }
</script>
</body>
</html>