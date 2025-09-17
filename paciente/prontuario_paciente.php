<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once '../controller/iniciar.php';
$seguranca->proteger_pagina('paciente');

// 2. BUSCA DE DADOS DO PACIENTE LOGADO
$paciente_id = $_SESSION['usuario_id'];
$stmtPaciente = $conexaoBD->proteger_sql("SELECT * FROM user_pacientes WHERE id = ?", [$paciente_id]);
$dadosPaciente = $stmtPaciente->get_result()->fetch_assoc();
$stmtPaciente->close();

if (!$dadosPaciente) {
    die("Erro crítico: Não foi possível carregar os dados do paciente.");
}

// 3. BUSCA DO HISTÓRICO DE CONSULTAS (APENAS AS VISÍVEIS)
$sqlConsultas = "SELECT
                    c.id_consulta, c.data_consulta, c.anamnese, c.exame_fisico, c.diagnostico_final, c.tratamento_proposto,
                    m.nome as nome_medico
                 FROM consultas c
                 JOIN user_medicos m ON c.id_medico = m.id
                 WHERE c.paciente_id = ? AND c.visivel_para_paciente = TRUE
                 ORDER BY c.data_consulta DESC";

$stmtConsultas = $conexaoBD->proteger_sql($sqlConsultas, [$paciente_id]);
$consultas = $stmtConsultas->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtConsultas->close();

// 4. BUSCA DOS DOCUMENTOS ANEXADOS
$stmtDocumentos = $conexaoBD->proteger_sql("SELECT * FROM documentos_paciente WHERE id_paciente = ? ORDER BY data_upload DESC", [$paciente_id]);
$documentos = $stmtDocumentos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtDocumentos->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário Eletrônico - MediCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style_prontuario_paciente.css">
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
                <img src="../uploads/fotos_perfil/<?= $dadosPaciente['foto_perfil'] ?>" class="profile-img" alt="Foto de perfil">
                <div class="patient-name"><?= htmlspecialchars($dadosPaciente['nome_paciente']); ?></div>
                <div class="patient-id">Paciente #<?= str_pad($dadosPaciente['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <div class="nav-section">
            <a href="dashBoard_Paciente.php" class="nav-link">
                <i class="fas fa-chart-line"></i>
                Painel de Controle
            </a>
            <a href="prontuario_paciente.php" class="nav-link active">
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
                Encerrar Sessão
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-file-medical"></i>
                Prontuário Eletrônico
            </h1>
            <p class="page-subtitle">Acesse seu histórico médico completo, consultas e documentos de forma segura e organizada.</p>
            
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge online">
                    <i class="fas fa-shield-alt"></i>
                    Dados Protegidos por Criptografia
                </span>
                <span class="text-muted">
                    <i class="fas fa-sync-alt me-1"></i>
                    Atualizado em tempo real
                </span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="prontuarioTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="consultas-tab" data-bs-toggle="tab" data-bs-target="#consultas" type="button">
                    <i class="fas fa-stethoscope"></i>
                    Histórico de Consultas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                    <i class="fas fa-folder-open"></i>
                    Meus Documentos
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="prontuarioTabContent">
            
            <!-- Consultas Tab -->
            <div class="tab-pane fade show active" id="consultas" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <?php if (count($consultas) > 0): ?>
                            <div class="accordion consultation-accordion" id="accordionConsultas">
                                <?php foreach ($consultas as $index => $consulta): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $consulta['id_consulta'] ?>">
                                                <div class="w-100 d-flex justify-content-between align-items-center me-3">
                                                    <div class="consultation-date">
                                                        <i class="fas fa-calendar-check"></i>
                                                        Consulta em <?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?>
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
                                                <div class="medical-section">
                                                    <div class="section-title">
                                                        <i class="fas fa-comments"></i>
                                                        Queixas e Histórico (Anamnese)
                                                    </div>
                                                    <div class="section-content">
                                                        <?= nl2br(htmlspecialchars($consulta['anamnese'] ?: 'Não informado')) ?>
                                                    </div>
                                                </div>

                                                <div class="medical-section">
                                                    <div class="section-title">
                                                        <i class="fas fa-search"></i>
                                                        Exame Físico
                                                    </div>
                                                    <div class="section-content">
                                                        <?= nl2br(htmlspecialchars($consulta['exame_fisico'] ?: 'Não informado')) ?>
                                                    </div>
                                                </div>

                                                <div class="medical-section diagnosis">
                                                    <div class="section-title">
                                                        <i class="fas fa-diagnoses"></i>
                                                        Diagnóstico Final
                                                    </div>
                                                    <div class="section-content">
                                                        <?= htmlspecialchars($consulta['diagnostico_final'] ?: 'Não informado') ?>
                                                    </div>
                                                </div>

                                                <div class="medical-section treatment">
                                                    <div class="section-title">
                                                        <i class="fas fa-prescription-bottle-alt"></i>
                                                        Tratamento Proposto
                                                    </div>
                                                    <div class="section-content">
                                                        <?= nl2br(htmlspecialchars($consulta['tratamento_proposto'] ?: 'Nenhuma recomendação específica.')) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>Nenhuma Consulta Registrada</h5>
                                <p class="lead">Nenhum registro de consulta foi compartilhado com você.</p>
                                <p class="text-muted">Peça ao seu médico para marcar a consulta como "visível para o paciente" em seu sistema.</p>
                                <div class="mt-4">
                                    <span class="status-badge online">
                                        <i class="fas fa-info-circle"></i>
                                        Aguardando liberação médica
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Documentos Tab -->
            <div class="tab-pane fade" id="documentos" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                         <?php if (count($documentos) > 0): ?>
                            <div class="document-list">
                                <?php foreach ($documentos as $doc): ?>
                                    <?php
                                    $extensao = strtolower(pathinfo($doc['nome_arquivo'], PATHINFO_EXTENSION));
                                    $iconClass = 'default';
                                    if (in_array($extensao, ['pdf'])) $iconClass = 'pdf';
                                    elseif (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) $iconClass = 'image';
                                    elseif (in_array($extensao, ['doc', 'docx'])) $iconClass = 'doc';
                                    ?>
                                    <a href="uploads/documentos_pacientes/<?= htmlspecialchars($doc['nome_arquivo']) ?>" target="_blank" class="list-group-item">
                                        <div class="document-icon <?= $iconClass ?>">
                                            <i class="fas fa-<?= $iconClass === 'pdf' ? 'file-pdf' : ($iconClass === 'image' ? 'file-image' : ($iconClass === 'doc' ? 'file-word' : 'file-alt')) ?>"></i>
                                        </div>
                                        <div class="document-info">
                                            <div class="document-title"><?= htmlspecialchars($doc['titulo_documento']) ?></div>
                                            <div class="document-date">
                                                <i class="fas fa-calendar me-1"></i>
                                                Enviado em: <?= date('d/m/Y H:i', strtotime($doc['data_upload'])) ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-external-link-alt download-icon"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                         <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-file-medical"></i>
                                <h5>Nenhum Documento Anexado</h5>
                                <p class="lead">Nenhum documento ou exame foi anexado ao seu prontuário.</p>
                                <p class="text-muted">Seus exames, laudos e documentos médicos aparecerão aqui quando anexados por um profissional de saúde.</p>
                                <div class="mt-4">
                                    <span class="status-badge online">
                                        <i class="fas fa-upload"></i>
                                        Aguardando upload de documentos
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animação suave para as abas
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    const tabPane = document.querySelector(target);
                    
                    if (tabPane) {
                        tabPane.style.opacity = '0';
                        tabPane.style.transform = 'translateY(20px)';
                        
                        setTimeout(() => {
                            tabPane.style.transition = 'all 0.3s ease';
                            tabPane.style.opacity = '1';
                            tabPane.style.transform = 'translateY(0)';
                        }, 50);
                    }
                });
            });

            // Efeito hover nos itens de documento
            const documentItems = document.querySelectorAll('.list-group-item');
            
            documentItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.01)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>