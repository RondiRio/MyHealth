<?php
require_once '../controller/iniciar.php';
require_once '../controller/Paciente.php';
// require_once '../publics/seguranca.php';

$seguranca = new Seguranca();
$seguranca->proteger_pagina('paciente');
$csrf_token = $seguranca->gerar_csrf_token();

$conexaoBD = new ConexaoBD(); 
$conn = $conexaoBD->getConexao();
$paciente_id = $_SESSION['usuario_id'];
$paciente = new Paciente($conn, $paciente_id);
$dadosPaciente = $paciente->getProfileInfo();

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        $tipo_meta = $_POST['tipo_meta'];
        $descricao_meta = $_POST['descricao_meta'];
        $frequencia = $_POST['frequencia'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'] ?? null;
        $observacoes = $_POST['observacoes'] ?? '';

        $sql = "INSERT INTO metas_saude (id_paciente, tipo_meta, descricao_meta, frequencia, data_inicio, data_fim, observacoes, status, data_criacao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'ativa', NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $paciente_id, $tipo_meta, $descricao_meta, $frequencia, $data_inicio, $data_fim, $observacoes);
        
        if ($stmt->execute()) {
            $mensagem = 'Meta de saúde criada com sucesso!';
            $tipo_mensagem = 'success';
        } else {
            $mensagem = 'Erro ao criar meta de saúde.';
            $tipo_mensagem = 'danger';
        }
    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Meta de Saúde - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/Style_dashPaciente.css">
</head>
<body>
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
            <a href="dashBoard_Paciente.php" class="nav-link">
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

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-target me-3"></i>Adicionar Meta de Saúde</h2>
                        <a href="dashBoard_Paciente.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                        </a>
                    </div>

                    <?php if ($mensagem): ?>
                        <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                            <?= $mensagem ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Meta</label>
                                            <select class="form-select" name="tipo_meta" required>
                                                <option value="">Selecione...</option>
                                                <option value="exercicio">Exercício Físico</option>
                                                <option value="alimentacao">Alimentação</option>
                                                <option value="hidratacao">Hidratação</option>
                                                <option value="sono">Sono</option>
                                                <option value="medicamento">Medicamento</option>
                                                <option value="peso">Controle de Peso</option>
                                                <option value="habito">Mudança de Hábito</option>
                                                <option value="mental">Saúde Mental</option>
                                                <option value="outro">Outro</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Frequência</label>
                                            <select class="form-select" name="frequencia" required>
                                                <option value="">Selecione...</option>
                                                <option value="diaria">Diária</option>
                                                <option value="semanal">Semanal</option>
                                                <option value="mensal">Mensal</option>
                                                <option value="uma_vez">Uma vez</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descrição da Meta</label>
                                    <input type="text" class="form-control" name="descricao_meta" 
                                           placeholder="Ex: Caminhar 30 minutos, Beber 2L de água, Dormir 8 horas..." required>
                                    <div class="form-text">Seja específico e mensurável</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data de Início</label>
                                            <input type="date" class="form-control" name="data_inicio" 
                                                   value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data de Fim <small class="text-muted">(opcional)</small></label>
                                            <input type="date" class="form-control" name="data_fim">
                                            <div class="form-text">Deixe em branco para meta contínua</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações e Motivação <small class="text-muted">(opcional)</small></label>
                                    <textarea class="form-control" name="observacoes" rows="3" 
                                              placeholder="Por que esta meta é importante? Como pretende alcançá-la?"></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus me-2"></i>Criar Meta
                                    </button>
                                    <a href="dashBoard_Paciente.php" class="btn btn-secondary btn-lg ms-3">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Exemplos de Metas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-lightbulb me-2"></i>Exemplos de Metas SMART</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Exercício Físico:</h6>
                                    <ul class="small">
                                        <li>Caminhar 10.000 passos por dia</li>
                                        <li>Fazer exercícios 3x por semana</li>
                                        <li>Subir escadas em vez do elevador</li>
                                    </ul>
                                    
                                    <h6>Alimentação:</h6>
                                    <ul class="small">
                                        <li>Comer 5 porções de frutas/verduras por dia</li>
                                        <li>Reduzir açúcar em 50%</li>
                                        <li>Fazer 5 refeições pequenas</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Hidratação:</h6>
                                    <ul class="small">
                                        <li>Beber 2 litros de água por dia</li>
                                        <li>Evitar refrigerantes</li>
                                        <li>Beber um copo de água ao acordar</li>
                                    </ul>
                                    
                                    <h6>Sono:</h6>
                                    <ul class="small">
                                        <li>Dormir 8 horas por noite</li>
                                        <li>Ir para cama antes das 23h</li>
                                        <li>Evitar telas 1h antes de dormir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dica sobre metas SMART -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-star me-2"></i>Dica: Use o método SMART</h6>
                        <p class="small mb-0">
                            <strong>S</strong>pecífica - <strong>M</strong>ensurável - <strong>A</strong>tingível - 
                            <strong>R</strong>elevante - <strong>T</strong>emporal
                            <br>
                            Exemplo: Em vez de "fazer exercício", prefira "caminhar 30 minutos, 5 vezes por semana"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sugestões baseadas no tipo selecionado
        document.querySelector('select[name="tipo_meta"]').addEventListener('change', function() {
            const descricaoInput = document.querySelector('input[name="descricao_meta"]');
            const sugestoes = {
                'exercicio': 'Caminhar 30 minutos por dia',
                'alimentacao': 'Comer 5 porções de frutas e verduras por dia',
                'hidratacao': 'Beber 2 litros de água por dia',
                'sono': 'Dormir 8 horas por noite',
                'medicamento': 'Tomar medicamento nos horários corretos',
                'peso': 'Perder 2kg em 2 meses',
                'habito': 'Parar de fumar em 30 dias',
                'mental': 'Meditar 10 minutos por dia'
            };
            
            if (sugestoes[this.value]) {
                descricaoInput.placeholder = 'Ex: ' + sugestoes[this.value];
            }
        });

        // Validar data de fim
        document.querySelector('input[name="data_fim"]').addEventListener('change', function() {
            const dataInicio = document.querySelector('input[name="data_inicio"]').value;
            if (this.value && dataInicio && this.value <= dataInicio) {
                alert('A data de fim deve ser posterior à data de início');
                this.value = '';
            }
        });
    </script>
</body>
</html>