<?php
// 1. FUNDAÇÃO: INICIALIZAÇÃO E SEGURANÇA BÁSICA
// Esta linha é essencial. Ela nos dá o $seguranca, $conexaoBD e protege a página.
require_once 'iniciar.php';

// $seguranca->proteger_pagina('medico');

// Valida o token CSRF e o método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou expirado. Tente novamente.'];
    header('Location: configuracao_medico.php');
    exit;
}

// 2. FUNÇÃO DEDICADA PARA O UPLOAD DA FOTO
// Mover a lógica complexa para uma função deixa o código principal muito mais legível.
function gerenciarUploadFotoPerfil(array $arquivoFoto, int $medico_id, ConexaoBD $conexaoBD): ?string {
    $diretorioUpload = 'uploads/fotos_perfil/';

    // Se nenhum arquivo novo foi enviado, não faz nada.
    if ($arquivoFoto['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Validações de segurança (tamanho e tipo)
    if ($arquivoFoto['size'] > 2 * 1024 * 1024) { // 2MB
        throw new Exception('Erro: O arquivo de imagem é muito grande (máximo 2MB).');
    }
    if (!in_array(mime_content_type($arquivoFoto['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
        throw new Exception('Erro: Formato de arquivo não permitido (use JPG, PNG ou GIF).');
    }

    // Cria um nome de arquivo novo e seguro
    $extensao = strtolower(pathinfo($arquivoFoto['name'], PATHINFO_EXTENSION));
    $novoNome = 'medico_' . $medico_id . '_' . time() . '.' . $extensao;
    $caminhoCompleto = $diretorioUpload . $novoNome;
    
    // Apaga a foto antiga antes de salvar a nova
    $stmtFotoAntiga = $conexaoBD->proteger_sql("SELECT foto_perfil FROM user_medicos WHERE id = ?", [$medico_id]);
    $fotoAntiga = $stmtFotoAntiga->get_result()->fetch_assoc()['foto_perfil'] ?? null;
    if ($fotoAntiga && $fotoAntiga !== 'default.png' && file_exists($diretorioUpload . $fotoAntiga)) {
        unlink($diretorioUpload . $fotoAntiga);
    }
    $stmtFotoAntiga->close();

    // Move o arquivo para o destino final
    if (!move_uploaded_file($arquivoFoto['tmp_name'], $caminhoCompleto)) {
        throw new Exception('Erro ao salvar a nova foto de perfil.');
    }

    return $novoNome; // Retorna o nome do novo arquivo para ser salvo no banco
}


// 3. EXECUÇÃO PRINCIPAL
try {
    $medico_id = $_SESSION['usuario_id'];

    // Sanitiza os dados do formulário
    $dados = $seguranca->sanitizar_entrada([
        'email' => $_POST['email'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'status_atual' => $_POST['status_atual'] ?? ''
    ]);

    // Processa o upload da foto. A função retorna o novo nome do arquivo ou null.
    $novoNomeFoto = gerenciarUploadFotoPerfil($_FILES['foto_perfil'] ?? [], $medico_id, $conexaoBD);

    // Monta a query de atualização do banco
    $sql = "UPDATE user_medicos SET email = ?, telefone = ?, endereco = ?, cidade = ?, estado = ?, status_atual = ?";
    $params = array_values($dados);

    // Se uma nova foto foi enviada, adiciona ao SQL e aos parâmetros
    if ($novoNomeFoto) {
        $sql .= ", foto_perfil = ?";
        $params[] = $novoNomeFoto;
    }

    $sql .= " WHERE id = ?";
    $params[] = $medico_id;

    // Executa a atualização
    $stmt = $conexaoBD->proteger_sql($sql, $params);
    $stmt->close();

    // Define a mensagem de sucesso
    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Perfil atualizado com sucesso!'];

} catch (Exception $e) {
    // Se qualquer parte do processo falhar (upload ou banco), captura o erro.
    error_log("Erro ao atualizar perfil do médico: " . $e->getMessage());
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $e->getMessage()];
}

// 4. REDIRECIONAMENTO
// Redireciona de volta para a página de configurações, que exibirá a mensagem.
header('Location: ../medico/configuracao_medico.php');
exit;