<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once 'iniciar.php';
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
    <title>Meu Prontuário - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { display: flex; background-color: #f8f9fa; }
        .sidebar { width: 280px; background: #343a40; color: white; min-height: 100vh; }
        .content { flex: 1; padding: 2rem; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #495057; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: #495057; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <div class="text-center">
            <h3 class="text-white mb-3">Minha Saúde</h3>
            <img src="uploads/fotos_perfil/<?= htmlspecialchars($dadosPaciente['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
            <h5 class="mt-3 mb-4 text-white"><?= htmlspecialchars($dadosPaciente['nome_paciente']); ?></h5>
        </div>
        <a href="dashBoard_Paciente.php" class="nav-link mb-2"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="prontuario_paciente.php" class="nav-link active mb-2"><i class="fas fa-file-medical me-2"></i> Meu Prontuário</a>
        <a href="configuracao_paciente.php" class="nav-link mb-2"><i class="fas fa-cog me-2"></i> Configurações</a>
        <form action="logout.php" method="post" class="w-100 mt-auto">
            <button type="submit" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i> Sair</button>
        </form>
    </div>

    <div class="content">
        <h1 class="h2 mb-4 fw-bold">Meu Prontuário Eletrônico</h1>
        
        <ul class="nav nav-tabs mb-3" id="prontuarioTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="consultas-tab" data-bs-toggle="tab" data-bs-target="#consultas" type="button">Histórico de Consultas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">Meus Documentos</button>
            </li>
        </ul>

        <div class="tab-content" id="prontuarioTabContent">
            
            <div class="tab-pane fade show active" id="consultas" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (count($consultas) > 0): ?>
                            <div class="accordion" id="accordionConsultas">
                                <?php foreach ($consultas as $index => $consulta): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $consulta['id_consulta'] ?>">
                                                <strong>Consulta em <?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?></strong>
                                                <span class="ms-auto text-muted"> com Dr(a). <?= htmlspecialchars($consulta['nome_medico']) ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?= $consulta['id_consulta'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordionConsultas">
                                            <div class="accordion-body">
                                                <p><strong>Queixas e Histórico (Anamnese):</strong> <?= nl2br(htmlspecialchars($consulta['anamnese'] ?: 'Não informado')) ?></p>
                                                <p><strong>Exame Físico:</strong> <?= nl2br(htmlspecialchars($consulta['exame_fisico'] ?: 'Não informado')) ?></p>
                                                <hr>
                                                <p><strong>Diagnóstico Final:</strong> <?= htmlspecialchars($consulta['diagnostico_final'] ?: 'Não informado') ?></p>
                                                <p class="mb-1"><strong>Tratamento Proposto:</strong></p>
                                                <div class="p-3 bg-light rounded">
                                                    <?= nl2br(htmlspecialchars($consulta['tratamento_proposto'] ?: 'Nenhuma recomendação específica.')) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <p class="lead">Nenhum registro de consulta foi compartilhado com você.</p>
                                <p class="text-muted">Peça ao seu médico para marcar a consulta como "visível para o paciente".</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="documentos" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                         <?php if (count($documentos) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($documentos as $doc): ?>
                                    <a href="uploads/documentos_pacientes/<?= htmlspecialchars($doc['nome_arquivo']) ?>" target="_blank" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        <strong><?= htmlspecialchars($doc['titulo_documento']) ?></strong>
                                        <small class="text-muted d-block">Upload em: <?= date('d/m/Y', strtotime($doc['data_upload'])) ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                         <?php else: ?>
                            <div class="text-center p-5">
                                <i class="fas fa-file-excel fa-4x text-muted mb-3"></i>
                                <p class="lead">Nenhum documento ou exame anexado.</p>
                                <p class="text-muted">Seus exames e laudos aparecerão aqui quando anexados por um profissional.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>