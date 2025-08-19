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

        .search-section {
            background: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
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

        .prontuario-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .prontuario-header {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            color: #fff;
            padding: 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .paciente-info h2 {
            margin: 0 0 0.5rem 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .paciente-info span {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-nova-consulta {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-nova-consulta:hover {
            background: rgba(255,255,255,0.3);
            color: #fff;
            transform: translateY(-2px);
        }

        .nav-tabs {
            background: #f8f9fa;
            border-bottom: none;
            padding: 0 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--medical-gray);
            font-weight: 600;
            padding: 1.5rem 2rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-tabs .nav-link:hover {
            border: none;
            background: transparent;
            color: var(--medical-blue);
        }

        .nav-tabs .nav-link.active {
            background: transparent;
            color: var(--medical-blue);
            border: none;
            border-bottom: 3px solid var(--medical-blue);
        }

        .tab-content {
            padding: 2rem;
        }

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

        .document-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid #f8f9fa;
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

        .document-card i {
            color: var(--medical-teal);
            margin-bottom: 1rem;
        }

        .document-card:hover i {
            color: var(--medical-blue);
        }

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
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 1rem; }
            .prontuario-header { flex-direction: column; gap: 1rem; text-align: center; }
            .nav-tabs .nav-link { padding: 1rem; }
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
            <div class="prontuario-section">
                <div class="prontuario-header">
                    <div class="paciente-info">
                        <h2 id="paciente-nome"></h2>
                        <span id="paciente-info"></span>
                    </div>
                    <div>
                        <a href="consulta.php" class="btn-nova-consulta">
                            <i class="fas fa-plus me-2"></i> 
                            Nova Consulta
                        </a>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="prontuarioTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#consultas">
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
                    <div class="tab-pane fade show active" id="consultas">
                        <div class="accordion" id="accordionConsultas"></div>
                    </div>
                    
                    <div class="tab-pane fade" id="documentos">
                        <div id="lista-documentos" class="row g-4"></div>
                    </div>
                    
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
                
                // Cabeçalho do prontuário
                document.getElementById('paciente-nome').textContent = escapeHTML(data.paciente.nome);
                let infoExtra = '';
                if (data.paciente.data_nascimento) {
                    const idade = new Date().getFullYear() - new Date(data.paciente.data_nascimento).getFullYear();
                    infoExtra = `${idade} anos • CPF: ${escapeHTML(data.paciente.cpf)}`;
                }
                document.getElementById('paciente-info').textContent = infoExtra;

                // Aba de Consultas
                const accordionConsultas = document.getElementById('accordionConsultas');
                if (data.consultas && data.consultas.length > 0) {
                    accordionConsultas.innerHTML = data.consultas.map((c, i) => `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button ${i > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#c-${c.id_consulta}" aria-expanded="${i === 0}">
                                    <i class="fas fa-calendar-check me-3"></i>
                                    ${new Date(c.data_consulta).toLocaleDateString('pt-BR')} - ${escapeHTML(c.diagnostico_final) || 'Consulta de rotina'}
                                </button>
                            </h2>
                            <div id="c-${c.id_consulta}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}" data-bs-parent="#accordionConsultas">
                                <div class="accordion-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-user-md me-2"></i>Médico:</span>
                                                <span class="dados-value">Dr. ${escapeHTML(c.nome_medico)}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-stethoscope me-2"></i>Especialidade:</span>
                                                <span class="dados-value">${escapeHTML(c.especialidade) || 'N/A'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-clipboard-list me-2"></i>Anamnese:</span>
                                                <span class="dados-value">${escapeHTML(c.anamnese) || 'Não informado'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-search me-2"></i>Exame Físico:</span>
                                                <span class="dados-value">${escapeHTML(c.exame_fisico) || 'Não realizado'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-diagnoses me-2"></i>Diagnóstico:</span>
                                                <span class="dados-value">${escapeHTML(c.diagnostico_final) || 'Não informado'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="dados-row">
                                                <span class="dados-label"><i class="fas fa-pills me-2"></i>Tratamento:</span>
                                                <span class="dados-value">${escapeHTML(c.tratamento_proposto) || 'Não prescrito'}</span>
                                            </div>
                                        </div>
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
                                <i class="fas fa-file-medical fa-3x"></i>
                                <h6 class="mt-2 mb-0">${escapeHTML(d.titulo_documento)}</h6>
                                <small class="text-muted">${escapeHTML(d.tipo_documento) || 'Documento'}</small>
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
        });

        // Adicionar classe de status badge
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
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
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>