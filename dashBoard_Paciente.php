<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once 'iniciar.php';
require_once 'Paciente.php'; // Usar require_once é mais seguro

// =======================================================
// LINHAS DE CONEXÃO QUE FALTAVAM
// =======================================================
// Instancia sua classe de conexão com o banco de dados
$conexaoBD = new ConexaoBD(); 
// Pega o objeto de conexão mysqli e o armazena na variável $conn
$conn = $conexaoBD->getConexao();
// =======================================================

// Garante que apenas usuários do tipo 'paciente' acessem esta página
$seguranca->proteger_pagina(tipoUsuarioPermitido: 'paciente');

// 2. INICIALIZAÇÃO DO PACIENTE
// Define o ID do paciente a partir da sessão LOGO NO INÍCIO
$paciente_id = $_SESSION['usuario_id'];

// Agora $conn existe e é um objeto mysqli válido ao criar o objeto Paciente
$paciente = new Paciente($conn, $paciente_id);


// 3. BUSCA DE DADOS PELA CLASSE PACIENTE
$profileInfo = $paciente->getProfileInfo();

// Verificação de segurança: se não encontrar o perfil, interrompe a execução.
if (!$profileInfo) {
    die("Erro crítico: Não foi possível carregar os dados do paciente. Por favor, faça login novamente.");
}

$allergies = $paciente->getAllergies();
$latestVitals = $paciente->getLatestVitalSigns();
$medicationsToday = $paciente->getMedicationsForToday();
$healthGoals = $paciente->getHealthGoals();
$timelineEvents = $paciente->getHealthTimeline();
$consultas = $paciente->getConsultasVisiveis();

// 4. CONTAGEM DE ESTATÍSTICAS
$totalConsultas = count($consultas);
$consultasRecentes = $paciente->getContagemConsultasRecentes(); 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Paciente - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/Style_dashPaciente.css">
    <style>
        
    </style>
</head>
<body>
    <?php 
        //     echo"<style=
        // 'backgound-color: #000' pre>";
        //     print_r($profileInfo);
        //     echo'</pre>';
        ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>
            <div class="patient-profile">
                <img src="uploads/fotos_perfil/<?= htmlspecialchars($profileInfo['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
                <div class="patient-name"><?= htmlspecialchars($profileInfo['nome_paciente']); ?></div>
                <div class="patient-id">Paciente #<?= str_pad($profileInfo['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>
        
        <div class="nav-section">
            <a href="#" class="nav-link active">
                <i class="fas fa-chart-line"></i>
                Painel de Controle
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-file-medical-alt"></i>
                Meu Prontuário
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-user-cog"></i>
                Configurações
            </a>
        </div>

        <button class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            Encerrar Sessão
        </button>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>
            <div class="patient-profile">
                <img src="uploads/fotos_perfil/<?= htmlspecialchars($profileInfo['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
                <div class="patient-name"><?= htmlspecialchars($profileInfo['nome_paciente']); ?></div>
                <div class="patient-id">Paciente #<?= str_pad($profileInfo['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <div class="nav-section">
            <a href="dashBoard_Paciente.php" class="nav-link active">
                <i class="fas fa-chart-line"></i>
                Painel de Controle
            </a>
            <a href="prontuario_paciente.php" class="nav-link">
                <i class="fas fa-file-medical-alt"></i>
                Meu Prontuário
            </a>
            <a href="configuracao_paciente.php" class="nav-link">
                <i class="fas fa-user-cog"></i>
                Configurações
            </a>
        </div>

        <form action="logout.php" method="post">
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Encerrar Sessão</span>
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Header Section -->
         
        <div class="header-section">
            <h1 class="welcome-title">
                <i class="fas fa-user-circle me-3"></i>
                Olá, <?= htmlspecialchars(explode(' ', $profileInfo['nome_paciente'])[0]); ?>!
            </h1>
            <p class="welcome-subtitle">Bem-vindo ao seu portal de saúde. Acompanhe suas consultas e cuide do seu bem-estar.</p>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="fas fa-shield-alt me-2"></i>
                    Sistema Seguro
                </span>
                <span class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Última atividade: <?= date('d/m/Y H:i') ?>
                </span>
            </div>
        </div>

        <!-- Health Statistics -->
        <div class="health-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-value"><?= $totalConsultas ?></div>
                <div class="stat-label">Consultas Realizadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?= $consultasRecentes ?></div>
                <div class="stat-label">Consultas Recentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?= date('Y') - date('Y', strtotime($profileInfo['data_nascimento'] ?? '1990-01-01')) ?></div>
                <div class="stat-label">Idade</div>
            </div>
        </div>

        <!-- Consultation History -->
        <div class="main-card">
            
            <div class="card-header">
                <h4>
                    <i class="fas fa-history"></i>
                    Histórico de Consultas Médicas
                </h4>
            </div>
            <div class="card-body">
                <?php if (count($consultas) > 0): ?>
                    <div class="accordion consultation-accordion" id="accordionConsultas">
                        <?php foreach ($consultas as $index => $consulta): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?= $consulta['id_consulta'] ?>"></h2>
                                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $consulta['id_consulta'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse-<?= $consulta['id_consulta'] ?>">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="consultation-date">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                <?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?>
                                            </div>
                                            <div class="consultation-doctor text-muted">
                                                <i class="fas fa-user-md me-1"></i>
                                                Dr(a). <?= htmlspecialchars($consulta['nome_medico']) ?>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $consulta['id_consulta'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading-<?= $consulta['id_consulta'] ?>" data-bs-parent="#accordionConsultas">
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <i class="fas fa-diagnoses me-2 text-primary"></i>
                                                Diagnóstico Final:
                                            </h6>
                                            <p class="mb-0"><?= htmlspecialchars($consulta['diagnostico_final'] ?: 'Não informado') ?></p>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold">
                                                <i class="fas fa-prescription-bottle-alt me-2 text-success"></i>
                                                Tratamento Proposto:
                                            </h6>
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($consulta['tratamento_proposto'] ?: 'Nenhuma recomendação registrada.')) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold">Nenhuma Consulta Registrada</h5>
                        <p class="text-muted">Você ainda não possui consultas médicas em seu histórico.</p>
                        <p class="text-muted">Assim que um médico registrar uma nova consulta e a tornar visível para você, ela aparecerá aqui automaticamente.</p>
                        <div class="mt-4">
                            <span class="badge bg-info fs-6 px-3 py-2">
                                <i class="fas fa-info-circle me-2"></i>
                                Entre em contato com sua clínica para mais informações
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="dashboard-grid">
            <div class="card today-summary">
                <div class="card-header"><i class="fas fa-calendar-day"></i> Resumo do Dia</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-pills me-2 text-primary"></i> Medicamentos de Hoje</h6>
                            <div class="medication-checklist">
                                <?php if (empty($medicationsToday)): ?>
                                    <p class="text-muted small">Nenhum medicamento programado para hoje.</p>
                                <?php else: ?>
                                    <?php foreach ($medicationsToday as $med): ?>
                                        <div class="checklist-item <?php echo $med['tomado'] ? 'completed' : ''; ?>">
                                            <input type="checkbox" id="med<?php echo $med['id']; ?>" <?php echo $med['tomado'] ? 'checked' : ''; ?> onchange="toggleMedication(this, <?php echo $med['id']; ?>)">
                                            <div class="medication-info">
                                                <div class="medication-name"><?php echo htmlspecialchars($med['nome_medicamento']) . ' ' . htmlspecialchars($med['dosagem']); ?></div>
                                                <div class="medication-time"><?php echo $med['horario']; ?> - <?php echo $med['tomado'] ? 'Tomado' : 'Pendente'; ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-target me-2 text-success"></i> Metas de Hoje</h6>
                            <div class="goals-checklist">
                                <?php if (empty($healthGoals)): ?>
                                    <p class="text-muted small">Nenhuma meta ativa.</p>
                                <?php else: ?>
                                    <?php foreach ($healthGoals as $goal): ?>
                                        <div class="checklist-item <?php echo $goal['concluida'] ? 'completed' : ''; ?>">
                                            <input type="checkbox" id="goal<?php echo $goal['id']; ?>" <?php echo $goal['concluida'] ? 'checked' : ''; ?> onchange="toggleGoal(this, <?php echo $goal['id']; ?>)">
                                            <div class="medication-info">
                                                <div class="medication-name"><?php echo htmlspecialchars($goal['descricao_meta']); ?></div>
                                                <div class="medication-time"><?php echo $goal['concluida'] ? 'Concluído' : 'Pendente'; ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-heartbeat"></i> Meus Sinais Vitais</div>
                <div class="card-body">
                    <?php if (isset($latestVitals['Pressão Arterial'])): $vital = $latestVitals['Pressão Arterial']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Pressão Arterial</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?>/<?php echo htmlspecialchars($vital['valor2']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="bpChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($latestVitals['Glicemia'])): $vital = $latestVitals['Glicemia']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Glicemia</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="glucoseChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($latestVitals['Peso'])): $vital = $latestVitals['Peso']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Peso</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="weightChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#vitalSignModal">Registrar Sinal Vital</button>
                    </div>
                </div>
            </div>

            <div class="card">
                 <div class="card-header"><i class="fas fa-exclamation-triangle"></i> Minhas Alergias</div>
                 <div class="card-body">
                    <h6 class="text-primary mb-2">Alergias Alimentares</h6>
                    <div class="allergy-list mb-3">
                        <?php foreach ($allergies['alimentares'] as $alergia): ?>
                            <div class="allergy-item">
                                <span><?php echo htmlspecialchars($alergia['nome_agente']); ?></span>
                                <div class="allergy-actions">
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteAllergy(<?php echo $alergia['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <h6 class="text-primary mb-2">Alergias Respiratórias</h6>
                    <div class="allergy-list mb-3">
                        <?php foreach ($allergies['respiratorias'] as $alergia): ?>
                             <div class="allergy-item">
                                <span><?php echo htmlspecialchars($alergia['nome_agente']); ?></span>
                                <div class="allergy-actions">
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteAllergy(<?php echo $alergia['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                     <div class="text-center">
                         <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#allergyModal"><i class="fas fa-plus me-2"></i> Adicionar Alergia</button>
                     </div>
                 </div>
            </div>

            <div class="timeline-section">
                <div class="card-header"><i class="fas fa-history"></i> Linha do Tempo da Saúde</div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if (empty($timelineEvents)): ?>
                            <p class="text-muted">Nenhum evento na sua linha do tempo ainda.</p>
                        <?php else: ?>
                            <?php foreach ($timelineEvents as $event): ?>
                                <div class="timeline-item" data-type="<?php echo $event['event_type']; ?>">
                                    <div class="timeline-icon">
                                        <i class="<?php echo $event['icon']; ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-date"><?php echo date('d \d\e F \d\e Y', strtotime($event['event_date'])); ?></div>
                                        <div class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                        <p class="text-muted mb-0"><?php echo $event['description']; // Cuidado: A query já trata o HTML, não usar htmlspecialchars aqui ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-actions">
        <div class="fab-menu" id="fabMenu">
            <a href="#" class="fab-option" data-bs-toggle="modal" data-bs-target="#vitalSignModal">
                <i class="fas fa-heartbeat"></i>
                Registrar Sinal Vital
            </a>
            <a href="#" class="fab-option" data-bs-toggle="modal" data-bs-target="#examModal">
                <i class="fas fa-upload"></i>
                Upload de Exame
            </a>
            <a href="#" class="fab-option" data-bs-toggle="modal" data-bs-target="#goalModal">
                <i class="fas fa-target"></i>
                Adicionar Meta
            </a>
        </div>
        <button class="fab-main" onclick="toggleFabMenu()">
            <i class="fas fa-plus" id="fabIcon"></i>
        </button>
    </div>

    <!-- Modals -->
    <!-- Activity Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Atividade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Atividade</label>
                            <select class="form-select">
                                <option>Exercício Físico</option>
                                <option>Alimentação</option>
                                <option>Medicamento</option>
                                <option>Sintoma</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" rows="3" placeholder="Descreva a atividade..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data e Hora</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Allergy Modal -->
    <div class="modal fade" id="allergyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Alergia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Alergia</label>
                            <select class="form-select" id="allergyType">
                                <option value="alimentar">Alimentar</option>
                                <option value="respiratoria">Respiratória</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Agente Causador</label>
                            <input type="text" class="form-control" placeholder="Ex: Camarão, Pólen, etc.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição dos Sintomas</label>
                            <textarea class="form-control" rows="3" placeholder="Descreva os sintomas que você sente..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Adicionar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vital Sign Modal -->
    <div class="modal fade" id="vitalSignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Sinal Vital</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Sinal Vital</label>
                            <select class="form-select">
                                <option>Pressão Arterial</option>
                                <option>Glicemia</option>
                                <option>Peso</option>
                                <option>Temperatura</option>
                                <option>Frequência Cardíaca</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <input type="text" class="form-control" placeholder="Ex: 120/80, 95, 68.5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unidade</label>
                            <input type="text" class="form-control" placeholder="Ex: mmHg, mg/dL, kg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data e Hora</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" rows="2" placeholder="Observações adicionais (opcional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Upload Modal -->
    <div class="modal fade" id="examModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload de Exame</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form >
                        <div class="mb-3">
                            <label class="form-label">Nome do Exame</label>
                            <input type="text" class="form-control" placeholder="Ex: Hemograma Completo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data do Exame</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Arquivo</label>
                            <input type="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Formatos aceitos: PDF, JPG, PNG (máx. 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" rows="2" placeholder="Observações sobre o exame (opcional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Fazer Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Goal Modal -->
    <div class="modal fade" id="goalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Meta</label>
                            <select class="form-select">
                                <option>Exercício Físico</option>
                                <option>Alimentação</option>
                                <option>Hidratação</option>
                                <option>Sono</option>
                                <option>Medicamento</option>
                                <option>Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição da Meta</label>
                            <input type="text" class="form-control" placeholder="Ex: Caminhar 30 minutos">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Frequência</label>
                            <select class="form-select">
                                <option>Diária</option>
                                <option>Semanal</option>
                                <option>Mensal</option>
                                <option>Uma vez</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Início</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" rows="2" placeholder="Detalhes adicionais sobre a meta"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Criar Meta</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
   //======================================================================
// Bloco 1: Funções de Comunicação com o Backend (AJAX/Fetch)
//======================================================================

/**
 * Função centralizada para enviar dados ao backend via Fetch API.
 * Ela automaticamente adiciona o token CSRF para segurança.
 * @param {FormData} formData - O objeto FormData com os dados a serem enviados.
 * @returns {Promise<object>} - A resposta JSON do servidor.
 */
async function sendData(formData) {
    try {
        // Adiciona o token CSRF a cada requisição (essencial para segurança)
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        formData.append('csrf_token', csrfToken);

        const response = await fetch('paciente_handler.php', {
            method: 'POST',
            body: formData
        });

        // Se a resposta não for OK (ex: erro 500, 404), lança um erro
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch Error:', error);
        // Retorna um objeto de erro padronizado para a UI
        return { success: false, message: 'Erro de conexão com o servidor. Verifique o console.' };
    }
}

//======================================================================
// Bloco 2: Funções de UI Originais (Mantidas e Otimizadas)
//======================================================================

function initializeMiniCharts() {
    // Código para inicializar os 3 mini-gráficos (Blood Pressure, Glucose, Weight)
    // Nenhuma alteração necessária aqui, seu código original foi mantido.
    const bpCtx = document.getElementById('bpChart')?.getContext('2d');
    if (bpCtx) new Chart(bpCtx, { type: 'line', data: { labels: ['', '', '', '', '', '', ''], datasets: [{ data: [125, 122, 120, 118, 120, 119, 120], borderColor: '#28a745', borderWidth: 2, pointRadius: 0, tension: 0.4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } } });
    const glucoseCtx = document.getElementById('glucoseChart')?.getContext('2d');
    if (glucoseCtx) new Chart(glucoseCtx, { type: 'line', data: { labels: ['', '', '', '', '', '', ''], datasets: [{ data: [98, 92, 95, 89, 93, 97, 95], borderColor: '#17a2b8', borderWidth: 2, pointRadius: 0, tension: 0.4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } } });
    const weightCtx = document.getElementById('weightChart')?.getContext('2d');
    if (weightCtx) new Chart(weightCtx, { type: 'line', data: { labels: ['', '', '', '', '', '', ''], datasets: [{ data: [69.2, 69.0, 68.8, 68.5, 68.7, 68.4, 68.5], borderColor: '#6f42c1', borderWidth: 2, pointRadius: 0, tension: 0.4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } } });
}

function animateNumbers() {
    // Animação para os valores dos sinais vitais
    const numbers = document.querySelectorAll('.vital-value');
    numbers.forEach(num => {
        num.style.opacity = '0';
        setTimeout(() => {
            num.style.opacity = '1';
            num.style.transform = 'scale(1.1)';
            setTimeout(() => { num.style.transform = 'scale(1)'; }, 200);
        }, Math.random() * 500);
    });
}

// Funções para marcar/desmarcar medicações e metas (agora com integração backend)
function toggleMedication(checkbox, logId) {
    // Lógica original da UI mantida
    const item = checkbox.closest('.checklist-item');
    const timeDiv = item.querySelector('.medication-time');
    item.classList.toggle('completed', checkbox.checked);
    // ... lógica para atualizar o texto do tempo ...

    // NOVA LÓGICA BACKEND:
    const formData = new FormData();
    formData.append('action', 'update_medication_status');
    formData.append('log_id', logId); // Você precisará adicionar o ID do log de medicação ao seu HTML
    formData.append('status', checkbox.checked);
    sendData(formData).then(result => {
        if (!result.success) alert(result.message); // Avisa o usuário se a atualização falhar
    });
}

function toggleGoal(checkbox, logId) {
    // Lógica similar a toggleMedication
}

// Funções de UI para a timeline, menu flutuante, e responsividade (mantidas)
function filterTimeline(type) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    document.querySelectorAll('.timeline-item').forEach(item => {
        item.style.display = (type === 'all' || item.dataset.type === type) ? 'block' : 'none';
    });
}

let fabOpen = false;
function toggleFabMenu() {
    fabOpen = !fabOpen;
    document.getElementById('fabMenu').classList.toggle('active', fabOpen);
    document.getElementById('fabIcon').style.transform = fabOpen ? 'rotate(45deg)' : 'rotate(0deg)';
}

function editAllergy(allergyId, allergyName, type, symptoms) {
    // Esta função agora prepara o modal para edição
    const modal = new bootstrap.Modal(document.getElementById('allergyModal'));
    const form = document.querySelector('#allergyModal form');
    form.querySelector('#allergyType').value = type;
    form.querySelector('input[type="text"]').value = allergyName;
    form.querySelector('textarea').value = symptoms;
    // Adicionar um campo oculto com o ID da alergia para o handler saber que é uma edição
    // form.querySelector('input[name="allergy_id"]').value = allergyId;
    modal.show();
}

function deleteAllergy(allergyId) {
    if (!confirm('Tem certeza que deseja remover esta alergia?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_allergy');
    formData.append('allergy_id', allergyId);
    
    sendData(formData).then(result => {
        alert(result.message);
        if (result.success) location.reload(); // Recarrega a página para ver a mudança
    });
}

//======================================================================
// Bloco 3: Inicialização e Event Listeners
//======================================================================

document.addEventListener('DOMContentLoaded', function() {
    // 1. Inicializa componentes de UI
    initializeMiniCharts();
    animateNumbers();

    // 2. Configura os event listeners para os modais
    
    // Modal de Alergia
    document.querySelector('#allergyModal .btn-primary').addEventListener('click', async () => {
        const form = document.querySelector('#allergyModal form');
        const formData = new FormData(form);
        formData.append('action', 'add_allergy'); // ou 'edit_allergy' se estiver editando
        // Adicione os names aos seus inputs HTML para que o FormData funcione:
        // Ex: <select name="tipo" id="allergyType">, <input name="nomeAgente">, <textarea name="sintomas">
        
        const result = await sendData(formData);
        alert(result.message);
        if (result.success) location.reload();
    });

    // Modal de Sinal Vital
    document.querySelector('#vitalSignModal .btn-primary').addEventListener('click', async () => {
        const form = document.querySelector('#vitalSignModal form');
        const formData = new FormData(form);
        formData.append('action', 'register_vital_sign');
        
        const result = await sendData(formData);
        alert(result.message);
        if (result.success) location.reload();
    });

    // Modal de Upload de Exame
    document.querySelector('#examModal .btn-primary').addEventListener('click', async () => {
        const form = document.querySelector('#examModal form');
        const formData = new FormData(form);
        formData.append('action', 'upload_exam');
        
        const result = await sendData(formData);
        alert(result.message);
        if (result.success) location.reload();
    });

    // 3. Configura outros listeners de UI
    
    // Fecha o menu flutuante se clicar fora
    document.addEventListener('click', function(event) {
        const fab = document.querySelector('.floating-actions');
        if (fab && !fab.contains(event.target) && fabOpen) {
            toggleFabMenu();
        }
    });

    // Lógica para o botão de menu em telas móveis
    if (window.innerWidth <= 768) {
        const content = document.querySelector('.content');
        if (content && !document.querySelector('.mobile-menu-btn')) {
            const mobileMenuBtn = document.createElement('button');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            mobileMenuBtn.className = 'btn btn-primary position-fixed mobile-menu-btn';
            mobileMenuBtn.style.cssText = 'top: 1rem; left: 1rem; z-index: 1001;';
            mobileMenuBtn.onclick = () => document.querySelector('.sidebar').classList.toggle('show');
            content.prepend(mobileMenuBtn);
        }
    }
});
</script>

</body>
</html>
