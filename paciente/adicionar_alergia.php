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
    
        $tipo_alergia = $_POST['tipo_alergia'];
        $nome_agente = $_POST['nome_agente'];
        $sintomas = $_POST['sintomas'];
        $gravidade = $_POST['gravidade'];

        $sql = "INSERT INTO alergias (id_paciente, tipo_alergia, nome_agente, sintomas, severidade, data_registro) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $paciente_id, $tipo_alergia, $nome_agente, $sintomas, $gravidade);
        
        if ($stmt->execute()) {
            $mensagem = 'Alergia adicionada com sucesso!';
            $tipo_mensagem = 'success';
        } else {
            $mensagem = 'Erro ao adicionar alergia.';
            $tipo_mensagem = 'danger';
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Alergia - MyHealth</title>
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

        <form action="../controller/logout.php" method="post">
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
                        <h2><i class="fas fa-exclamation-triangle me-3"></i>Adicionar Alergia</h2>
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
                                            <label class="form-label">Tipo de Alergia</label>
                                            <select class="form-select" name="tipo_alergia" required>
                                                <option value="">Selecione...</option>
                                                <option value="alimentar">Alimentar</option>
                                                <option value="respiratoria">Respiratória</option>
                                                <option value="medicamentosa">Medicamentosa</option>
                                                <option value="contato">Contato/Pele</option>
                                                <option value="inseto">Picada de Inseto</option>
                                                <option value="outro">Outro</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Gravidade</label>
                                            <select class="form-select" name="gravidade" required>
                                                <option value="">Selecione...</option>
                                                <option value="leve">Leve</option>
                                                <option value="moderada">Moderada</option>
                                                <option value="grave">Grave</option>
                                                <option value="muito_grave">Muito Grave</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Agente Causador</label>
                                    <input type="text" class="form-control" name="nome_agente" 
                                           placeholder="Ex: Camarão, Pólen, Penicilina, Látex..." required>
                                    <div class="form-text">Seja específico sobre o que causa a alergia</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descrição dos Sintomas</label>
                                    <textarea class="form-control" name="sintomas" rows="4" 
                                              placeholder="Descreva os sintomas que você sente quando exposto ao agente causador..." required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-plus me-2"></i>Adicionar Alergia
                                    </button>
                                    <a href="dashBoard_Paciente.php" class="btn btn-secondary btn-lg ms-3">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Alerta Importante -->
                    <div class="alert alert-warning mt-4">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Importante!</h5>
                        <p class="mb-0">
                            Mantenha sempre essa informação atualizada e comunique suas alergias a todos os profissionais de saúde 
                            que você consultar. Em caso de alergia grave, considere usar uma pulseira de identificação médica.
                        </p>
                    </div>

                    <!-- Dicas sobre tipos de alergia -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle me-2"></i>Tipos de Alergia</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Alimentar:</h6>
                                    <p class="small">Leite, ovos, amendoim, frutos do mar, soja, etc.</p>
                                    
                                    <h6>Respiratória:</h6>
                                    <p class="small">Pólen, ácaros, fungos, pelos de animais, etc.</p>
                                    
                                    <h6>Medicamentosa:</h6>
                                    <p class="small">Penicilina, aspirina, anti-inflamatórios, etc.</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Contato/Pele:</h6>
                                    <p class="small">Látex, níquel, cosméticos, detergentes, etc.</p>
                                    
                                    <h6>Picada de Inseto:</h6>
                                    <p class="small">Abelhas, vespas, formigas, mosquitos, etc.</p>
                                    
                                    <h6>Gravidade:</h6>
                                    <ul class="small">
                                        <li><strong>Leve:</strong> Coceira, vermelhidão</li>
                                        <li><strong>Moderada:</strong> Inchaço, dificuldade respiratória leve</li>
                                        <li><strong>Grave:</strong> Reação anafilática, emergência médica</li>
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