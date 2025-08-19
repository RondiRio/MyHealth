<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once 'iniciar.php';
$seguranca->proteger_pagina(tipoUsuarioPermitido: 'paciente');

// 2. BUSCA DE DADOS DO PACIENTE LOGADO
$paciente_id = $_SESSION['usuario_id'];
$stmtPaciente = $conexaoBD->proteger_sql("SELECT * FROM user_pacientes WHERE id = ?", [$paciente_id]);
$dadosPaciente = $stmtPaciente->get_result()->fetch_assoc();
$stmtPaciente->close();

if (!$dadosPaciente) {
    die("Erro crítico: Não foi possível carregar os dados do paciente. Por favor, faça login novamente.");
}

// 3. BUSCA DO HISTÓRICO DE CONSULTAS (APENAS AS VISÍVEIS)
$sqlConsultas = "SELECT
                    c.*,
                    m.nome as nome_medico
                 FROM consultas c
                 JOIN user_medicos m ON c.id_medico = m.id
                 WHERE c.paciente_id = ? AND c.visivel_para_paciente = TRUE
                 ORDER BY c.data_consulta DESC";

$stmtConsultas = $conexaoBD->proteger_sql($sqlConsultas, [$paciente_id]);
$consultas = $stmtConsultas->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtConsultas->close();

// 4. CONTAGEM DE ESTATÍSTICAS
$totalConsultas = count($consultas);
$consultasRecentes = count(array_filter($consultas, function ($c) {
    return strtotime($c['data_consulta']) > strtotime('-30 days');
}));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Paciente - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/Style_dashPaciente.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>
            <div class="patient-profile">
                <img src="uploads/fotos_perfil/<?= htmlspecialchars($dadosPaciente['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
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

        <form action="logout.php" method="post">
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                Encerrar Sessão
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
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $consulta['id_consulta'] ?>">
                                        <div class="w-100 d-flex justify-content-between align-items-center me-3">
                                            <div>
                                                <div class="consultation-date">
                                                    <i class="fas fa-calendar-alt me-2"></i>
                                                    <?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?>
                                                </div>
                                            </div>
                                            <div class="consultation-doctor">
                                                <i class="fas fa-user-md me-1"></i>
                                                Dr(a). <?= htmlspecialchars($consulta['nome_medico']) ?>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $consulta['id_consulta'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordionConsultas">
                                    <div class="accordion-body">
                                        <div class="consultation-detail">
                                            <strong>
                                                <i class="fas fa-diagnoses"></i>
                                                Diagnóstico Final:
                                            </strong>
                                            <div class="consultation-content">
                                                <?= htmlspecialchars($consulta['diagnostico_final'] ?: 'Não informado') ?>
                                            </div>
                                        </div>

                                        <div class="consultation-detail">
                                            <strong>
                                                <i class="fas fa-prescription-bottle-alt"></i>
                                                Tratamento Proposto:
                                            </strong>
                                            <div class="consultation-content">
                                                <?= nl2br(htmlspecialchars($consulta['tratamento_proposto'] ?: 'Nenhuma recomendação específica registrada.')) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-medical"></i>
                        <h5>Nenhuma Consulta Registrada</h5>
                        <p class="lead">Você ainda não possui consultas médicas em seu histórico.</p>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animação dos números das estatísticas
        document.addEventListener('DOMContentLoaded', function() {
            const statValues = document.querySelectorAll('.stat-value');

            statValues.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
                if (finalValue > 0) {
                    let currentValue = 0;
                    const increment = Math.ceil(finalValue / 20);

                    const timer = setInterval(() => {
                        currentValue += increment;
                        if (currentValue >= finalValue) {
                            currentValue = finalValue;
                            clearInterval(timer);
                        }
                        stat.textContent = currentValue;
                    }, 50);
                }
            });

            // Efeito hover nos cards de estatísticas
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>

</html>