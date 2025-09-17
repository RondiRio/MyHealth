<?php
require_once '../controller/iniciar.php';
require_once '../controller/Paciente.php';
require_once '../publics/seguranca.php';

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
        $nome_exame = $_POST['nome_exame'];
        $data_exame = $_POST['data_exame'];
        $observacoes = $_POST['observacoes'] ?? '';
        
        // Upload do arquivo
        $arquivo_nome = '';
        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
            $arquivo_temp = $_FILES['arquivo']['tmp_name'];
            $arquivo_original = $_FILES['arquivo']['name'];
            $arquivo_tamanho = $_FILES['arquivo']['size'];
            $arquivo_tipo = $_FILES['arquivo']['type'];
            
            // Validações
            $extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            $extensao = strtolower(pathinfo($arquivo_original, PATHINFO_EXTENSION));
            
            if (!in_array($extensao, $extensoes_permitidas)) {
                $mensagem = 'Formato de arquivo não permitido. Use: PDF, JPG, PNG';
                $tipo_mensagem = 'danger';
            } elseif ($arquivo_tamanho > 10 * 1024 * 1024) { // 10MB
                $mensagem = 'Arquivo muito grande. Máximo: 10MB';
                $tipo_mensagem = 'danger';
            } else {
                // Gerar nome único
                $arquivo_nome = 'exame_' . $paciente_id . '_' . time() . '.' . $extensao;
                $caminho_upload = '../uploads/exames/' . $arquivo_nome;
                
                // Criar diretório se não existir
                if (!file_exists('../uploads/exames/')) {
                    mkdir('../uploads/exames/', 0755, true);
                }
                
                if (move_uploaded_file($arquivo_temp, $caminho_upload)) {
                    // Salvar no banco
                    $sql = "INSERT INTO exames (id_paciente, nome_exame, data_exame, arquivo_nome, arquivo_original, observacoes, data_upload) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssss", $paciente_id, $nome_exame, $data_exame, $arquivo_nome, $arquivo_original, $observacoes);
                    
                    if ($stmt->execute()) {
                        $mensagem = 'Exame enviado com sucesso!';
                        $tipo_mensagem = 'success';
                    } else {
                        $mensagem = 'Erro ao salvar exame no banco de dados.';
                        $tipo_mensagem = 'danger';
                        unlink($caminho_upload); // Remove arquivo se falhou no BD
                    }
                } else {
                    $mensagem = 'Erro ao fazer upload do arquivo.';
                    $tipo_mensagem = 'danger';
                }
            }
        } else {
            $mensagem = 'Por favor, selecione um arquivo para upload.';
            $tipo_mensagem = 'danger';
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Exame - MyHealth</title>
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
                        <h2><i class="fas fa-upload me-3"></i>Upload de Exame</h2>
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
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nome do Exame</label>
                                            <input type="text" class="form-control" name="nome_exame" 
                                                   placeholder="Ex: Hemograma Completo, Raio-X Tórax..." required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data do Exame</label>
                                            <input type="date" class="form-control" name="data_exame" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Arquivo do Exame</label>
                                    <input type="file" class="form-control" name="arquivo" 
                                           accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">
                                        Formatos aceitos: PDF, JPG, PNG | Tamanho máximo: 10MB
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações <small class="text-muted">(opcional)</small></label>
                                    <textarea class="form-control" name="observacoes" rows="3" 
                                              placeholder="Observações sobre o exame, médico solicitante, etc."></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upload me-2"></i>Fazer Upload
                                    </button>
                                    <a href="dashBoard_Paciente.php" class="btn btn-secondary btn-lg ms-3">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informações sobre upload -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle me-2"></i>Informações sobre Upload</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Formatos Aceitos:</h6>
                                    <ul class="small">
                                        <li><strong>PDF:</strong> Ideal para laudos e relatórios</li>
                                        <li><strong>JPG/PNG:</strong> Para imagens de exames</li>
                                    </ul>
                                    
                                    <h6>Tamanho do Arquivo:</h6>
                                    <p class="small">Máximo de 10MB por arquivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Dicas:</h6>
                                    <ul class="small">
                                        <li>Certifique-se que o arquivo está legível</li>
                                        <li>Use nomes descritivos para os exames</li>
                                        <li>Inclua a data correta do exame</li>
                                        <li>Adicione observações relevantes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segurança e Privacidade -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-shield-alt me-2"></i>Segurança e Privacidade</h6>
                        <p class="small mb-0">
                            Seus exames são armazenados com segurança e apenas você e os médicos autorizados 
                            podem acessá-los. Nunca compartilhamos suas informações médicas com terceiros.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview do arquivo selecionado
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;
                
                // Mostrar informações do arquivo
                let info = document.querySelector('.file-info');
                if (!info) {
                    info = document.createElement('div');
                    info.className = 'file-info mt-2 p-2 bg-light rounded';
                    this.parentNode.appendChild(info);
                }
                
                info.innerHTML = `
                    <small>
                        <i class="fas fa-file me-1"></i>
                        <strong>${fileName}</strong> (${fileSize} MB)
                    </small>
                `;
            }
        });
    </script>
</body>
</html>