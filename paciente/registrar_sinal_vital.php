<?php
require_once '../controller/iniciar.php';
require_once '../controller/Paciente.php';

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
   
        $tipo_sinal = $_POST['tipo_sinal'];
        $valor1 = $_POST['valor1'];
        $valor2 = $_POST['valor2'] ?? null;
        $unidade = $_POST['unidade'];
        $data_hora = $_POST['data_hora'];
        $observacoes = $_POST['observacoes'] ?? '';

        $sql = "INSERT INTO sinais_vitais (id_paciente, tipo, valor1, valor2, unidade, data_registro, observacoes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $paciente_id, $tipo_sinal, $valor1, $valor2, $unidade, $data_hora, $observacoes);
        
        if ($stmt->execute()) {
            $mensagem = 'Sinal vital registrado com sucesso!';
            $tipo_mensagem = 'success';
        } else {
            $mensagem = 'Erro ao registrar sinal vital.';
            $tipo_mensagem = 'danger';
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Sinal Vital - MyHealth</title>
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
                        <h2><i class="fas fa-heartbeat me-3"></i>Registrar Sinal Vital</h2>
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
                                            <label class="form-label">Tipo de Sinal Vital</label>
                                            <select class="form-select" name="tipo_sinal" required>
                                                <option value="">Selecione...</option>
                                                <option value="Pressão Arterial">Pressão Arterial</option>
                                                <option value="Glicemia">Glicemia</option>
                                                <option value="Peso">Peso</option>
                                                <option value="Temperatura">Temperatura</option>
                                                <option value="Frequência Cardíaca">Frequência Cardíaca</option>
                                                <option value="Altura">Altura</option>
                                                <option value="IMC">IMC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data e Hora</label>
                                            <input type="datetime-local" class="form-control" name="data_hora" 
                                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Valor Principal</label>
                                            <input type="text" class="form-control" name="valor1" 
                                                   placeholder="Ex: 120, 95, 68.5" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Valor Secundário <small class="text-muted">(opcional)</small></label>
                                            <input type="text" class="form-control" name="valor2" 
                                                   placeholder="Ex: 80 (para PA)">
                                            <div class="form-text">Usado para pressão arterial (120/80)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Unidade</label>
                                            <input type="text" class="form-control" name="unidade" 
                                                   placeholder="Ex: mmHg, mg/dL, kg, °C" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações <small class="text-muted">(opcional)</small></label>
                                    <textarea class="form-control" name="observacoes" rows="3" 
                                              placeholder="Observações adicionais sobre a medição"></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Registrar Sinal Vital
                                    </button>
                                    <a href="dashBoard_Paciente.php" class="btn btn-secondary btn-lg ms-3">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Dicas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-lightbulb me-2"></i>Dicas para Medição</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Pressão Arterial:</h6>
                                    <ul class="small">
                                        <li>Meça em repouso, após 5 min sentado</li>
                                        <li>Braço apoiado na altura do coração</li>
                                        <li>Use o formato: 120 (valor1) / 80 (valor2)</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Glicemia:</h6>
                                    <ul class="small">
                                        <li>Jejum: normal até 99 mg/dL</li>
                                        <li>Pós-prandial: até 140 mg/dL</li>
                                        <li>Anote se foi em jejum ou após refeição</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>