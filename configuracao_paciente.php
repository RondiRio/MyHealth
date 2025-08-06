<?php
require_once 'iniciar.php';

// Protege a página para garantir que apenas pacientes logados possam acessá-la.
$seguranca->proteger_pagina('paciente');

// Busca todos os dados do paciente que está logado na sessão.
$paciente_id = $_SESSION['usuario_id'];
$stmt = $conexaoBD->proteger_sql("SELECT * FROM user_pacientes WHERE id = ?", [$paciente_id]);
$dadosPaciente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dadosPaciente) {
    die("Erro: Não foi possível carregar os dados do paciente.");
}

// Prepara o sistema de notificação para feedback (ex: "Perfil atualizado!").
$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Configurações - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 fw-bold">Minhas Configurações</h1>
                <a href="dashboardPaciente.php" class="btn btn-secondary">Voltar ao Dashboard</a>
            </div>

            <?php if ($notificacao): ?>
                <div class="alert alert-<?= $notificacao['tipo'] === 'sucesso' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="processa_configuracao_paciente.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            
                            <div class="col-md-4 text-center">
                                <h5 class="mb-3">Foto de Perfil</h5>
                                <img src="uploads/fotos_perfil/<?= htmlspecialchars($dadosPaciente['foto_perfil'] ?: 'default-paciente.png') ?>" 
                                     alt="Foto de Perfil" 
                                     class="img-thumbnail rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="mb-3">
                                    <label for="foto_perfil" class="form-label">Alterar foto</label>
                                    <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="nome_paciente" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="nome_paciente" name="nome_paciente" value="<?= htmlspecialchars($dadosPaciente['nome_paciente']) ?>" readonly>
                                        <div class="form-text">O nome completo não pode ser alterado por aqui.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cpf" name="cpf" value="<?= htmlspecialchars($dadosPaciente['cpf']) ?>" readonly>
                                    </div>
                                     <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($dadosPaciente['email']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($dadosPaciente['telefone']) ?>" maxlength="15" pattern="\(\d{2}\)\d{5}-\d{4}" placeholder="(00)91234-5678" oninput="mascaraTelefone(this)">
                                        <script>
                                        function mascaraTelefone(campo) {
                                            let v = campo.value.replace(/\D/g, '');
                                            if (v.length > 11) v = v.slice(0, 11);
                                            if (v.length > 6) {
                                                campo.value = `(${v.slice(0,2)})${v.slice(2,7)}-${v.slice(7)}`;
                                            } else if (v.length > 2) {
                                                campo.value = `(${v.slice(0,2)})${v.slice(2)}`;
                                            } else if (v.length > 0) {
                                                campo.value = `(${v}`;
                                            }
                                        }
                                        </script>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($dadosPaciente['data_nascimento']) ?>">
                                    </div>
                                     <div class="col-12">
                                        <label for="endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="endereco" name="endereco" value="<?= htmlspecialchars($dadosPaciente['endereco']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                           <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>