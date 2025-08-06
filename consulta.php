<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');

// Busca os dados do médico logado para exibir no cabeçalho
$stmt = $conexaoBD->proteger_sql("SELECT nome, crm FROM user_medicos WHERE id = ?", [$_SESSION['usuario_id']]);
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
    <title>Prontuário de Atendimento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="text-center mb-4">
                <h1 class="h2 fw-bold">Prontuário Eletrônico de Atendimento</h1>
                <p class="text-muted">Médico Responsável: Dr. <?= htmlspecialchars($medico['nome']) ?></p>
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
                        <!-- Navegação em Abas -->
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
                        <!-- Conteúdo das Abas -->
                        <div class="tab-content" id="prontuarioTabContent">
                            
                            <!-- Aba 1: Identificação -->
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

                            <!-- Aba 2: Histórico Médico -->
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

                            <!-- Aba 3: Exame Físico -->
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

                            <!-- Aba 4: Diagnóstico e Conduta -->
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

                            <!-- Aba 5: Anexos -->
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
                    <button type="submit" class="btn btn-primary btn-lg">Salvar Consulta no Prontuário</button>
                    <a href="dashboard_medico.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
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
