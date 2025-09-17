<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once '../controller/iniciar.php';
require_once '../controller/Paciente.php'; 
require_once '../publics/seguranca.php';

$seguranca = new Seguranca();
$csrf_token = $seguranca->gerar_csrf_token();

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$url_completa = "$protocolo://$host$uri";

// 2. CONEXÃO COM BANCO DE DADOS
$conexaoBD = new ConexaoBD(); 
$conn = $conexaoBD->getConexao();

// 3. PROTEÇÃO DE PÁGINA
$seguranca->proteger_pagina(tipoUsuarioPermitido: 'paciente');

// 4. INICIALIZAÇÃO DO PACIENTE
$paciente_id = $_SESSION['usuario_id'];
$paciente = new Paciente($conn, $paciente_id);

// 5. BUSCA DE DADOS
$dadosPaciente = $paciente->getProfileInfo();

// Verificação de segurança
if (!$dadosPaciente) {
    die("Erro crítico: Não foi possível carregar os dados do paciente. Por favor, faça login novamente.");
}

$allergies = $paciente->getAllergies();
$latestVitals = $paciente->getLatestVitalSigns();
$medicationsToday = $paciente->getMedicationsForToday();
$healthGoals = $paciente->getHealthGoals();
$timelineEvents = $paciente->getHealthTimeline();
$consultas = $paciente->getConsultasVisiveis();

// 6. CONTAGEM DE ESTATÍSTICAS
$totalConsultas = count($consultas);
$consultasRecentes = $paciente->getContagemConsultasRecentes();

// print_r($allergies)
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
    <link rel="stylesheet" href="../css/Style_dashPaciente.css">
</head>
<body>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>

            <div class="patient-profile">
                <img src="../uploads/fotos_perfil/<?= htmlspecialchars($dadosPaciente['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
                <div class="patient-name"><?= htmlspecialchars($dadosPaciente['nome_paciente']); ?></div>
                <div class="patient-id">Paciente #<?= str_pad($dadosPaciente['id'], 5, '0', STR_PAD_LEFT) ?></div>
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

        <form action="../controller/logout.php" method="post">
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
                Olá, <?= htmlspecialchars(explode(' ', $dadosPaciente['nome_paciente'])[0]); ?>!
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
                <div class="stat-value"><?= date('Y') - date('Y', strtotime($dadosPaciente['data_nascimento'] ?? '1990-01-01')) ?></div>
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
                                <h2 class="accordion-header" id="heading-<?= $consulta['id_consulta'] ?>">
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

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Today Summary Card -->
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

            <!-- Vital Signs Card -->
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
                    <?php if (isset($latestVitals['Temperatura'])): $vital = $latestVitals['Temperatura']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Temperatura</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="tempChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($latestVitals['Frequência Cardíaca'])): $vital = $latestVitals['Frequência Cardíaca']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Frequência Cardíaca</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="heartRateChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($latestVitals['Altura'])): $vital = $latestVitals['Altura']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>Altura</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="heightChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($latestVitals['IMC'])): $vital = $latestVitals['IMC']; ?>
                    <div class="vital-sign">
                        <div class="vital-info">
                            <h6>IMC</h6>
                            <div class="vital-value"><?php echo htmlspecialchars($vital['valor1']); ?> <?php echo htmlspecialchars($vital['unidade']); ?></div>
                            <div class="vital-date"><?php echo htmlspecialchars($vital['data_formatada']); ?></div>
                        </div>
                        <canvas class="mini-chart" id="imcChart"></canvas>
                    </div>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="registrar_sinal_vital.php" class="btn btn-outline-primary btn-sm">Registrar Sinal Vital</a>
                    </div>
                </div>
            </div>

            <!-- Allergies Card -->
            <div class="card">
                <div class="card-header"><i class="fas fa-exclamation-triangle"></i> Minhas Alergias</div>
                <div class="card-body">
                    <h6 class="text-primary mb-2">Alergias Alimentares</h6>
                    <div class="allergy-list mb-3">
                        
                        <div class="allergy-item">
                                <span>
                            <?php foreach ($allergies as $categoria => $lista): ?>
    <?php foreach ($lista as $alergia): ?>
        <?php if ($alergia['tipo_alergia'] === 'alimentar'): ?>
            <span><?php echo htmlspecialchars($alergia['nome_agente']); ?></span>
            <div class="allergy-actions">
                <button class="btn btn-outline-danger btn-sm" 
                        onclick="deleteAllergy(<?php echo $alergia['id']; ?>)"><span><i class="fas fa-trash"></i></span>
                    
                </button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

                    </div>
                    <h6 class="text-primary mb-2">Alergias Respiratórias</h6>
                    <div class="allergy-list mb-3">
                            <?php if (!empty($allergies['respiratoria'])): ?>
    <?php foreach ($allergies['respiratoria'] as $alergia): ?>
        <div class="allergy-item">
            <span><?php echo htmlspecialchars($alergia['nome_agente']); ?></span>
            <div class="allergy-actions">
                <button class="btn btn-outline-danger btn-sm" 
                        onclick="deleteAllergy(<?php echo $alergia['id']; ?>)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

                    </div>
                    <div class="text-center">
                        <a href="adicionar_alergia.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-2"></i> Adicionar Alergia
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Timeline Section -->
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
                                        <p class="text-muted mb-0"><?php echo $event['description']; ?></p>
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
            <a href="registrar_sinal_vital.php" class="fab-option">
                <i class="fas fa-heartbeat"></i>
                Registrar Sinal Vital
            </a>
            <a href="upload_exame.php" class="fab-option">
                <i class="fas fa-upload"></i>
                Upload de Exame
            </a>
            <a href="adicionar_meta.php" class="fab-option">
                <i class="fas fa-target"></i>
                Adicionar Meta
            </a>
        </div>
        <button class="fab-main" onclick="toggleFabMenu()">
            <i class="fas fa-plus" id="fabIcon"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFabMenu() {
            const fabMenu = document.getElementById('fabMenu');
            const fabIcon = document.getElementById('fabIcon');
            
            fabMenu.classList.toggle('active');
            fabIcon.classList.toggle('fa-times');
            fabIcon.classList.toggle('fa-plus');
        }

        function toggleMedication(checkbox, medicationId) {
            // Implementar lógica para marcar/desmarcar medicamento
            console.log('Medication toggled:', medicationId, checkbox.checked);
        }

        function toggleGoal(checkbox, goalId) {
            // Implementar lógica para marcar/desmarcar meta
            console.log('Goal toggled:', goalId, checkbox.checked);
        }

        function deleteAllergy(allergyId) {
            if (confirm('Tem certeza que deseja remover esta alergia?')) {
                // Implementar lógica para deletar alergia
                console.log('Delete allergy:', allergyId);
            }
        }
    </script>
</body>
</html>