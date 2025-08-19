<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');

// Busca os dados do médico logado para exibir no cabeçalho
$stmt = $conexaoBD->proteger_sql("SELECT id, nome, crm, foto_perfil FROM user_medicos WHERE id = ?", [$_SESSION['usuario_id']]);
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
    <title>Prontuário de Atendimento - MediCare</title>
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
            margin-bottom: 0.5rem;
        }

        .doctor-crm {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
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
        
        /* Estilos adicionais para o prontuário */
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 2rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }

        .nav-tabs .nav-link {
            color: var(--medical-gray);
            border: none;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .nav-tabs .nav-link:hover {
            background-color: transparent;
            color: var(--medical-blue);
            border-color: var(--medical-blue);
        }

        .nav-tabs .nav-link.active {
            color: var(--medical-blue);
            background-color: transparent;
            border-color: var(--medical-blue);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 90, 160, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--medical-blue), var(--medical-light-blue));
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 90, 160, 0.3);
            color: #fff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        
        .d-flex.justify-content-between.align-items-center.mb-4 h1 {
            color: var(--medical-blue);
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 1rem; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
            <h3>MediCare</h3>
        </div>
        <div class="doctor-profile">
            <img src="uploads/fotos_perfil/<?= htmlspecialchars($medico['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
            <div class="profile-name">Dr. <?= htmlspecialchars($medico['nome']); ?></div>
            <div class="doctor-crm">CRM: <?= htmlspecialchars($medico['crm']); ?></div>
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

    <form action="routes/logout.php" method="post">
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Encerrar Sessão
        </button>
    </form>
</div>

<div class="content">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold"><i class="fas fa-notes-medical me-2"></i>Novo Prontuário de Atendimento</h1>
                    <a href="dashboard_medico.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar ao Dashboard
                    </a>
                </div>

                <?php if ($notificacao): ?>
                    <div class="alert alert-<?= $notificacao['tipo'] === 'sucesso' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="registrar_consulta.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                    
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="prontuarioTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="identificacao-tab" data-bs-toggle="tab" data-bs-target="#identificacao" type="button">Identificação</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico" type="button">Histórico Médico</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="exame-tab" data-bs-toggle="tab" data-bs-target="#exame" type="button">Exame Físico</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="conduta-tab" data-bs-toggle="tab" data-bs-target="#conduta" type="button">Diagnóstico e Conduta</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="anexos-tab" data-bs-toggle="tab" data-bs-target="#anexos" type="button">Anexos</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content" id="prontuarioTabContent">
                                <div class="tab-pane fade show active" id="identificacao" role="tabpanel">
                                    <h5 class="mb-3">Dados da Consulta e Paciente</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="cpf_paciente" class="form-label">CPF do Paciente <span class="text-danger">*</span></label>
                                            <input type="text" id="cpf_paciente" name="cpf_paciente" class="form-control" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="nome_paciente" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                            <input type="text" id="nome_paciente" name="nome_paciente" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                            <input type="date" id="data_nascimento" name="data_nascimento" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="data_consulta" class="form-label">Data e Hora da Consulta <span class="text-danger">*</span></label>
                                            <input type="datetime-local" id="data_consulta" name="data_consulta" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="convenio" class="form-label">Convênio / Plano de Saúde</label>
                                            <input type="text" id="convenio" name="convenio" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="historico" role="tabpanel">
                                    <h5 class="mb-3">Histórico Médico do Paciente</h5>
                                    <div class="mb-3">
                                        <label for="queixa_principal" class="form-label">Queixa Principal (QP) e HDA</label>
                                        <textarea id="queixa_principal" name="anamnese" class="form-control" rows="4"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="historico_patologico" class="form-label">Histórico Patológico Pregresso (Doenças, Cirurgias, Alergias)</label>
                                        <textarea id="historico_patologico" name="historico_patologico" class="form-control" rows="4"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="historico_familiar" class="form-label">Histórico Familiar</label>
                                        <textarea id="historico_familiar" name="historico_familiar" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="exame" role="tabpanel">
                                    <h5 class="mb-3">Exame Físico</h5>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-3"><label class="form-label">PA</label><input type="text" name="sinais_vitais[pa]" class="form-control"></div>
                                        <div class="col-md-3"><label class="form-label">FC</label><input type="text" name="sinais_vitais[fc]" class="form-control"></div>
                                        <div class="col-md-3"><label class="form-label">Temp.</label><input type="text" name="sinais_vitais[temp]" class="form-control"></div>
                                        <div class="col-md-3"><label class="form-label">SpO₂</label><input type="text" name="sinais_vitais[spo2]" class="form-control"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exame_fisico_detalhado" class="form-label">Avaliação Geral e por Sistemas</label>
                                        <textarea id="exame_fisico_detalhado" name="exame_fisico" class="form-control" rows="8"></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="conduta" role="tabpanel">
                                    <h5 class="mb-3">Diagnóstico e Plano Terapêutico</h5>
                                    <div class="mb-3">
                                        <label for="hipotese_diagnostica" class="form-label">Hipóteses Diagnósticas</label>
                                        <textarea id="hipotese_diagnostica" name="hipotese_diagnostica" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="diagnostico_final" class="form-label">Diagnóstico Final (com CID)</label>
                                        <input type="text" id="diagnostico_final" name="diagnostico_final" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tratamento_proposto" class="form-label">Conduta / Plano Terapêutico (Prescrições, Encaminhamentos)</label>
                                        <textarea id="tratamento_proposto" name="tratamento_proposto" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="observacoes_privadas" class="form-label"><strong>Anotações Privadas do Médico</strong> (Não visível ao paciente)</label>
                                        <textarea id="observacoes_privadas" name="observacoes_privadas" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="anexos" role="tabpanel">
                                    <h5 class="mb-3">Anexos (Exames, Laudos, etc.)</h5>
                                    <div class="mb-3">
                                        <label for="documentos" class="form-label">Anexar arquivos (imagens, PDFs)</label>
                                        <input class="form-control" type="file" id="documentos" name="documentos[]" multiple>
                                        <div class="form-text">Você pode selecionar múltiplos arquivos.</div>
                                    </div>
                                    <hr>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="visivel_para_paciente" name="visivel_para_paciente" value="1">
                                        <label class="form-check-label" for="visivel_para_paciente"><strong>Tornar este registro de consulta visível para o paciente</strong></label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Salvar Consulta no Prontuário
                        </button>
                        <a href="dashboard_medico.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-ban me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script>
    // Aplica máscara de CPF
    IMask(document.getElementById('cpf_paciente'), { mask: '000.000.000-00' });
</script>
</body>
</html>