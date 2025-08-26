<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário Eletrônico - MediCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .search-section {
            background: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin: 0 2rem 2rem 2rem;
            text-align: center;
        }

        .search-title {
            color: var(--medical-blue);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .search-subtitle {
            color: var(--medical-gray);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .search-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-group-text {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            border: none;
            color: #fff;
            font-size: 1.1rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-left: none;
            padding: 1rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 90, 160, 0.25);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--medical-teal), #17a2b8);
            border: none;
            color: #fff;
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
            color: #fff;
        }

        /* PATIENT HEADER - Enhanced from second template */
        .patient-header {
            background: #fff;
            padding: 2rem;
            margin: 0 2rem 2rem 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }

        .patient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--medical-blue), var(--medical-light-blue));
        }

        .patient-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .patient-basic-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .patient-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--medical-blue);
            background: var(--medical-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .patient-details h2 {
            color: var(--medical-blue);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .patient-meta {
            color: var(--medical-gray);
            font-size: 0.95rem;
        }

        .critical-alerts {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .alert-section {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .alert-section h6 {
            margin: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--medical-gray);
        }

        .alert-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .alert-tag {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .alert-tag.drug-allergy {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-red);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-tag.chronic-disease {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-tag.none {
            background: rgba(40, 167, 69, 0.1);
            color: var(--medical-green);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .btn-nova-consulta {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-nova-consulta:hover {
            transform: translateY(-2px);
            color: #fff;
            box-shadow: 0 6px 20px rgba(44, 90, 160, 0.3);
        }

        /* TAB NAVIGATION */
        .main-content {
            flex: 1;
            padding: 0 2rem 2rem 2rem;
            overflow-y: auto;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2rem;
            background: none;
            padding: 0;
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
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .tab-pane {
            padding: 2rem;
        }

        /* WIDGETS */
        .widget {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .widget:last-child {
            margin-bottom: 0;
        }

        .widget-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .widget-title {
            color: var(--medical-blue);
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 1rem;
        }

        .chart-filters {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .chart-filter {
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            background: #fff;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chart-filter.active,
        .chart-filter:hover {
            background: var(--medical-blue);
            color: #fff;
            border-color: var(--medical-blue);
        }

        .activity-feed {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: #fff;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid var(--medical-teal);
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--medical-teal);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: var(--medical-blue);
            margin-bottom: 0.25rem;
        }

        .activity-description {
            color: var(--medical-gray);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.8rem;
            color: var(--medical-gray);
        }

        /* ACCORDION STYLES */
        .accordion-item {
            border: none;
            margin-bottom: 1rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .accordion-header button {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            color: var(--medical-blue);
            font-weight: 600;
            padding: 1.5rem 2rem;
            font-size: 1.1rem;
        }

        .accordion-header button:not(.collapsed) {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            color: #fff;
        }

        .accordion-body {
            background: #fff;
            border-top: 1px solid #dee2e6;
            padding: 2rem;
        }

        /* DOCUMENT CARDS */
        .document-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .document-card {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--medical-gray);
            display: block;
            height: 100%;
        }

        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: var(--medical-blue);
            color: var(--medical-blue);
            text-decoration: none;
        }

        .document-icon {
            font-size: 3rem;
            color: var(--medical-teal);
            margin-bottom: 0.5rem;
        }

        .document-card:hover .document-icon {
            color: var(--medical-blue);
        }

        .document-name {
            font-weight: 600;
            color: var(--medical-blue);
            margin-bottom: 0.25rem;
        }

        .document-date {
            font-size: 0.8rem;
            color: var(--medical-gray);
        }

        /* PATIENT DATA STYLES */
        .card-body-dados {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 2rem;
            border-radius: 12px;
        }

        .dados-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .dados-row:last-child {
            border-bottom: none;
        }

        .dados-label {
            font-weight: 600;
            color: var(--medical-blue);
            font-size: 1.1rem;
        }

        .dados-value {
            color: var(--medical-gray);
            font-weight: 500;
        }

        /* ALERT STYLES */
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1.5rem;
            font-weight: 500;
        }

        .alert-warning-custom {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            color: #d63031;
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, #ff7675, #fd79a8);
            color: #fff;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: #fff;
        }

        .d-none { display: none !important; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { 
                width: 100%; 
                min-height: auto; 
                position: relative;
            }
            .content { 
                margin-left: 0;
                padding: 1rem; 
            }
            .patient-info-row { 
                flex-direction: column; 
                gap: 1rem; 
                text-align: center; 
            }
            .nav-tabs .nav-link { padding: 1rem; }
            .header-section,
            .search-section,
            .patient-header,
            .main-content {
                margin-left: 1rem;
                margin-right: 1rem;
            }
        }
        .consulta-section {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px dashed #ddd;
}
.consulta-section:last-child {
    border-bottom: none;
}
.consulta-section h6 {
    font-weight: 600;
    color: #495057;
}
.consulta-section p {
    margin: 0.25rem 0 0;
    white-space: pre-line; /* Mantém quebras de linha na anamnese/exame */
}

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MediCare</h3>
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
            <a href="prontuario.php" class="nav-link active">
                <i class="fas fa-notes-medical"></i>
                Prontuários
            </a>
            <a href="configuracao_medico.php" class="nav-link">
                <i class="fas fa-user-cog"></i>
                Configurações
            </a>
        </div>

        <form action="routes/logout.php" method="post">
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
                <i class="fas fa-notes-medical"></i>
                Prontuário Eletrônico
            </h1>
            <p class="page-subtitle">Acesse e gerencie o histórico completo de consultas, documentos e dados dos seus pacientes de forma segura e organizada.</p>
            
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge online">
                    <i class="fas fa-shield-alt"></i>
                    Dados Protegidos LGPD
                </span>
                <span class="text-muted">
                    <i class="fas fa-database me-1"></i>
                    Sistema integrado
                </span>
            </div>
        </div>

        <!-- ETAPA 1: BUSCA DO PACIENTE -->
        <div id="busca-container">
            <div class="search-section">
                <h2 class="search-title">
                    <i class="fas fa-search me-2"></i>
                    Buscar Prontuário
                </h2>
                <p class="search-subtitle">Digite o CPF do paciente para carregar o histórico médico completo</p>
                
                <form id="formBuscaProntuario" class="search-form">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" id="cpf_busca" class="form-control" placeholder="000.000.000-00" required>
                    </div>
                    <button class="btn-search" type="submit">
                        <i class="fas fa-search me-2"></i>
                        Buscar Paciente
                    </button>
                </form>
                
                <div id="busca-feedback" class="mt-4"></div>
            </div>
        </div>

        <!-- ETAPA 2: EXIBIÇÃO DO PRONTUÁRIO (inicialmente oculto) -->
        <div id="prontuario-container" class="d-none">
            <!-- Patient Header (Enhanced) -->
            <div class="patient-header">
                <div class="patient-info-row">
                    <div class="patient-basic-info">
                        <div class="patient-avatar" id="patient-avatar-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="patient-details">
                            <h2 id="paciente-nome"></h2>
                            <div class="patient-meta">
                                <span id="paciente-info"></span><br>
                                <span id="paciente-contato"></span>
                            </div>
                        </div>
                    </div>

                    <div class="critical-alerts" id="critical-alerts-section">
                        <!-- Alerts will be populated dynamically -->
                    </div>

                    <div>
                        <a href="consulta.php" class="btn-nova-consulta">
                            <i class="fas fa-plus me-2"></i> 
                            Nova Consulta
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Tabbed Content -->
            <div class="main-content">
                <ul class="nav nav-tabs" id="prontuarioTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-chart-line me-2"></i>
                            Visão Geral
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#consultas">
                            <i class="fas fa-stethoscope me-2"></i>
                            Consultas
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentos">
                            <i class="fas fa-file-medical me-2"></i>
                            Documentos
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dados-pessoais">
                            <i class="fas fa-user me-2"></i>
                            Dados Pessoais
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Overview Tab (New) -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <!-- Evolution Charts Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <h3 class="widget-title">
                                    <i class="fas fa-chart-line"></i>
                                    Evolução dos Sinais Vitais
                                </h3>
                                <div class="chart-filters">
                                    <button class="chart-filter active" onclick="changeChartPeriod('30d')">30 dias</button>
                                    <button class="chart-filter" onclick="changeChartPeriod('6m')">6 meses</button>
                                    <button class="chart-filter" onclick="changeChartPeriod('1y')">1 ano</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="vitalSignsChart"></canvas>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Recent Activities Widget -->
                                <div class="widget">
                                    <div class="widget-header">
                                        <h3 class="widget-title">
                                            <i class="fas fa-activity"></i>
                                            Atividades Recentes
                                        </h3>
                                    </div>
                                    <div class="activity-feed" id="recent-activities">
                                        <!-- Activities will be populated dynamically -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Current Medications Widget -->
                                <div class="widget">
                                    <div class="widget-header">
                                        <h3 class="widget-title">
                                            <i class="fas fa-prescription-bottle-alt"></i>
                                            Medicamentos Atuais
                                        </h3>
                                    </div>
                                    <div id="current-medications">
                                        <!-- Medications will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Consultas Tab -->
                    <div class="tab-pane fade" id="consultas">
                        <div class="accordion" id="accordionConsultas"></div>
                    </div>
                    
                    <!-- Documentos Tab -->
                    <div class="tab-pane fade" id="documentos">
                        <div id="lista-documentos" class="row g-4"></div>
                    </div>
                    
                    <!-- Dados Pessoais Tab -->
                    <div class="tab-pane fade" id="dados-pessoais">
                        <div class="card-body-dados">
                            <div id="dados-paciente-detalhes"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para CPF
            IMask(document.getElementById('cpf_busca'), { mask: '000.000.000-00' });

            const formBusca = document.getElementById('formBuscaProntuario');
            const buscaFeedback = document.getElementById('busca-feedback');
            const buscaContainer = document.getElementById('busca-container');
            const prontuarioContainer = document.getElementById('prontuario-container');

            // Animações de entrada
            const animateElements = document.querySelectorAll('.header-section, .search-section');
            animateElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });

            formBusca.addEventListener('submit', function(e) {
                e.preventDefault();
                const cpf = document.getElementById('cpf_busca').value;
                const submitBtn = formBusca.querySelector('button');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';
                buscaFeedback.innerHTML = '';

                fetch('buscar_prontuario.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({cpf: cpf})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        preencherProntuario(data);
                        buscaContainer.style.transition = 'all 0.5s ease';
                        buscaContainer.style.opacity = '0';
                        
                        setTimeout(() => {
                            buscaContainer.classList.add('d-none');
                            prontuarioContainer.classList.remove('d-none');
                            prontuarioContainer.style.opacity = '0';
                            prontuarioContainer.style.transform = 'translateY(30px)';
                            
                            setTimeout(() => {
                                prontuarioContainer.style.transition = 'all 0.6s ease';
                                prontuarioContainer.style.opacity = '1';
                                prontuarioContainer.style.transform = 'translateY(0)';
                            }, 100);
                        }, 500);
                    } else {
                        buscaFeedback.innerHTML = `<div class="alert alert-warning-custom alert-custom"><i class="fas fa-exclamation-triangle me-2"></i>${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    buscaFeedback.innerHTML = `<div class="alert alert-danger-custom alert-custom"><i class="fas fa-times-circle me-2"></i>Ocorreu um erro ao buscar o prontuário.</div>`;
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-search me-2"></i>Buscar Paciente';
                });
            });

            function preencherProntuario(data) {
                const escapeHTML = (str) => !str ? '' : str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
                
                // Cabeçalho do prontuário aprimorado
                document.getElementById('paciente-nome').textContent = escapeHTML(data.paciente.nome);
                
                let infoExtra = '';
                let contatoExtra = '';
                if (data.paciente.data_nascimento) {
                    const idade = new Date().getFullYear() - new Date(data.paciente.data_nascimento).getFullYear();
                    infoExtra = `${idade} anos • ${escapeHTML(data.paciente.genero) || 'N/I'} • CPF: ${escapeHTML(data.paciente.cpf)}`;
                }
                
                if (data.paciente.email || data.paciente.telefone) {
                    contatoExtra = `
                        ${data.paciente.email ? `<i class="fas fa-envelope me-1"></i>${escapeHTML(data.paciente.email)}` : ''}
                        ${data.paciente.telefone ? `<span class="ms-3"><i class="fas fa-phone me-1"></i>${escapeHTML(data.paciente.telefone)}</span>` : ''}
                    `;
                }
                
                document.getElementById('paciente-info').textContent = infoExtra;
                document.getElementById('paciente-contato').innerHTML = contatoExtra;

                // Alertas críticos (simulados - você pode implementar com dados reais)
                const alertsSection = document.getElementById('critical-alerts-section');
                alertsSection.innerHTML = `
                    <div class="alert-section">
                        <h6>Alergias Medicamentosas</h6>
                        <div class="alert-tags">
                            <span class="alert-tag none">
                                <i class="fas fa-check"></i>
                                NENHUMA REGISTRADA
                            </span>
                        </div>
                    </div>
                    <div class="alert-section">
                        <h6>Doenças Crônicas</h6>
                        <div class="alert-tags">
                            <span class="alert-tag none">
                                <i class="fas fa-check"></i>
                                NENHUMA REGISTRADA
                            </span>
                        </div>
                    </div>
                `;

                // Aba de Visão Geral - Atividades Recentes
                const recentActivities = document.getElementById('recent-activities');
                recentActivities.innerHTML = `
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Última Consulta</div>
                            <div class="activity-description">${data.consultas && data.consultas.length > 0 ? escapeHTML(data.consultas[0].diagnostico_final) || 'Consulta realizada' : 'Nenhuma consulta registrada'}</div>
                            <div class="activity-time">${data.consultas && data.consultas.length > 0 ? new Date(data.consultas[0].data_consulta).toLocaleDateString('pt-BR') : 'N/A'}</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Documentos</div>
                            <div class="activity-description">${data.documentos ? data.documentos.length : 0} documento(s) no prontuário</div>
                            <div class="activity-time">Atualizado hoje</div>
                        </div>
                    </div>
                `;

                // Medicamentos atuais (simulado)
                const currentMedications = document.getElementById('current-medications');
                currentMedications.innerHTML = `
                    <div class="alert alert-info-custom alert-custom">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma prescrição ativa encontrada. Use a aba "Consultas" para visualizar tratamentos prescritos.
                    </div>
                `;

                // Aba de Consultas
                const accordionConsultas = document.getElementById('accordionConsultas');
                if (data.consultas && data.consultas.length > 0) {
    accordionConsultas.innerHTML = data.consultas.map((c, i) => `
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button ${i > 0 ? 'collapsed' : ''}" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#c-${c.id_consulta}" 
                        aria-expanded="${i === 0}">
                    <i class="fas fa-calendar-check me-3"></i>
                    ${new Date(c.data_consulta).toLocaleDateString('pt-BR')} - ${escapeHTML(c.diagnostico_final) || 'Consulta de rotina'}
                </button>
            </h2>
            <div id="c-${c.id_consulta}" 
                 class="accordion-collapse collapse ${i === 0 ? 'show' : ''}" 
                 data-bs-parent="#accordionConsultas">
                <div class="accordion-body">
                    
                    <div class="consulta-section">
                        <h6><i class="fas fa-user-md me-2"></i>Médico</h6>
                        <p>Dr. ${escapeHTML(c.nome_medico)}</p>
                    </div>

                    <div class="consulta-section">
                        <h6><i class="fas fa-stethoscope me-2"></i>Especialidade</h6>
                        <p>${escapeHTML(c.especialidade) || 'N/A'}</p>
                    </div>

                    <div class="consulta-section">
                        <h6><i class="fas fa-clipboard-list me-2"></i>Anamnese</h6>
                        <p>${escapeHTML(c.anamnese) || 'Não informado'}</p>
                    </div>

                    <div class="consulta-section">
                        <h6><i class="fas fa-search me-2"></i>Exame Físico</h6>
                        <p>${escapeHTML(c.exame_fisico) || 'Não realizado'}</p>
                    </div>

                    <div class="consulta-section">
                        <h6><i class="fas fa-diagnoses me-2"></i>Diagnóstico</h6>
                        <p>${escapeHTML(c.diagnostico_final) || 'Não informado'}</p>
                    </div>

                    <div class="consulta-section">
                        <h6><i class="fas fa-pills me-2"></i>Tratamento</h6>
                        <p>${escapeHTML(c.tratamento_proposto) || 'Não prescrito'}</p>
                    </div>

                </div>
            </div>
        </div>
    `).join('');
} else {
    accordionConsultas.innerHTML = '<div class="alert alert-info-custom alert-custom"><i class="fas fa-info-circle me-2"></i>Nenhum registro de consulta encontrado para este paciente.</div>';
}


                // Aba de Documentos
                const listaDocumentos = document.getElementById('lista-documentos');
                if (data.documentos && data.documentos.length > 0) {
                    listaDocumentos.innerHTML = data.documentos.map(d => `
                        <div class="col-md-4 col-lg-3">
                            <a href="uploads/documentos_pacientes/${escapeHTML(d.nome_arquivo)}" target="_blank" class="document-card">
                                <div class="document-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div class="document-name">${escapeHTML(d.titulo_documento)}</div>
                                <div class="document-date">${escapeHTML(d.tipo_documento) || 'Documento'}</div>
                            </a>
                        </div>
                    `).join('');
                } else {
                    listaDocumentos.innerHTML = '<div class="col-12"><div class="alert alert-info-custom alert-custom"><i class="fas fa-folder-open me-2"></i>Nenhum documento anexado ao prontuário deste paciente.</div></div>';
                }
                
                // Aba de Dados Pessoais
                const detalhesPaciente = document.getElementById('dados-paciente-detalhes');
                detalhesPaciente.innerHTML = `
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-id-card me-2"></i>CPF:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.cpf)}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-address-card me-2"></i>RG:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.rg) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-envelope me-2"></i>Email:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.email) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-phone me-2"></i>Telefone:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.telefone) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-birthday-cake me-2"></i>Data de Nascimento:</span>
                        <span class="dados-value">${data.paciente.data_nascimento ? new Date(data.paciente.data_nascimento).toLocaleDateString('pt-BR') : 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-venus-mars me-2"></i>Gênero:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.genero) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-map-marker-alt me-2"></i>Endereço:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.endereco) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-city me-2"></i>Cidade:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.cidade) || 'Não informado'}</span>
                    </div>
                    <div class="dados-row">
                        <span class="dados-label"><i class="fas fa-flag me-2"></i>Estado:</span>
                        <span class="dados-value">${escapeHTML(data.paciente.estado) || 'Não informado'}</span>
                    </div>
                `;

                // Inicializar gráfico de sinais vitais (simulado)
                initializeVitalSignsChart();
            }

            // Função para voltar à busca
            window.voltarBusca = function() {
                prontuarioContainer.style.transition = 'all 0.5s ease';
                prontuarioContainer.style.opacity = '0';
                
                setTimeout(() => {
                    prontuarioContainer.classList.add('d-none');
                    buscaContainer.classList.remove('d-none');
                    buscaContainer.style.opacity = '0';
                    
                    setTimeout(() => {
                        buscaContainer.style.transition = 'all 0.6s ease';
                        buscaContainer.style.opacity = '1';
                    }, 100);
                }, 500);
                
                // Limpar form
                document.getElementById('cpf_busca').value = '';
                buscaFeedback.innerHTML = '';
            };

            // Inicializar gráfico de sinais vitais
            function initializeVitalSignsChart() {
                const ctx = document.getElementById('vitalSignsChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                            datasets: [{
                                label: 'Pressão Sistólica',
                                data: [120, 125, 118, 130, 122, 128],
                                borderColor: 'rgb(44, 90, 160)',
                                backgroundColor: 'rgba(44, 90, 160, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Pressão Diastólica',
                                data: [80, 82, 78, 85, 81, 84],
                                borderColor: 'rgb(23, 162, 184)',
                                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Evolução da Pressão Arterial'
                                },
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    min: 60,
                                    max: 150
                                }
                            }
                        }
                    });
                }
            }

            // Função para alterar período do gráfico
            window.changeChartPeriod = function(period) {
                document.querySelectorAll('.chart-filter').forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
                // Aqui você pode implementar a lógica para alterar os dados do gráfico
                console.log('Período alterado para:', period);
            };
        });
    </script>
</body>
</html>