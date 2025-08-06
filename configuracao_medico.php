<?php
require_once 'iniciar.php';

// Protege a página
$seguranca->proteger_pagina('medico');

// Busca os dados atuais do médico para preencher o formulário
$medico_id = $_SESSION['usuario_id'];
$stmt = $conexaoBD->proteger_sql("SELECT * FROM user_medicos WHERE id = ?", [$medico_id]);
$dadosMedico = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dadosMedico) {
    die("Erro: Não foi possível encontrar os dados do médico.");
}

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Perfil - MyHealth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="text-center mb-4">Configurações do Perfil</h2>

            <?php if ($notificacao): ?>
                <div class="alert alert-<?= $notificacao['tipo'] === 'sucesso' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="Valida_configuracao_medico.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <h5 class="mb-3">Foto de Perfil</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <img src="uploads/fotos_perfil/<?= $seguranca->sanitizar_entrada($dadosMedico['foto_perfil'] ?: 'default.png') ?>" 
                                         alt="Foto de Perfil" 
                                         class="img-fluid rounded-circle border" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="mb-3">
                                    <label for="foto_perfil" class="form-label">Alterar foto</label>
                                    <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nome" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="<?= $seguranca->sanitizar_entrada($dadosMedico['nome']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="crm" class="form-label">CRM</label>
                                        <input type="text" class="form-control" id="crm" name="crm" value="<?= $seguranca->sanitizar_entrada($dadosMedico['crm']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= $seguranca->sanitizar_entrada($dadosMedico['email']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $seguranca->sanitizar_entrada($dadosMedico['telefone']) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label for="endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $seguranca->sanitizar_entrada($dadosMedico['endereco']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $seguranca->sanitizar_entrada($dadosMedico['cidade']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="estado" class="form-label">Estado</label>
                                        <input type="text" class="form-control" id="estado" name="estado" value="<?= $seguranca->sanitizar_entrada($dadosMedico['estado']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $seguranca->sanitizar_entrada($dadosMedico['data_nascimento']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status_atual">
                                            <option value="Ativo" <?= ($dadosMedico['status_atual'] === 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                                            <option value="Inativo" <?= ($dadosMedico['status_atual'] === 'Inativo') ? 'selected' : '' ?>>Inativo</option>
                                        </select>
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