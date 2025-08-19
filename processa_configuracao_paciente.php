<?php
require_once 'iniciar.php';

// Garante que apenas pacientes logados possam executar este script
$seguranca->proteger_pagina('paciente');

// Verifica CSRF e se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: configuracao_paciente.php');
    exit;
}

try {
    $paciente_id = $_SESSION['usuario_id'];

    // Sanitiza os dados editáveis do formulário
    $dados = $seguranca->sanitizar_entrada([
        'email' => $_POST['email'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'data_nascimento' => $_POST['data_nascimento'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
    ]);

    // Lógica para o upload da foto de perfil
    $nomeArquivoFoto = null;
    $diretorioUpload = 'uploads/fotos_perfil/';

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto_perfil'];
        
        // Validações de segurança do arquivo
        if ($foto['size'] > 2 * 1024 * 1024) throw new Exception('Erro: O arquivo de imagem é muito grande (máximo 2MB).');
        if (!in_array(mime_content_type($foto['tmp_name']), ['image/jpeg', 'image/png'])) throw new Exception('Erro: Formato de arquivo não permitido (use JPG ou PNG).');

        // Cria um nome de arquivo novo e seguro
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $nomeArquivoFoto = 'paciente_' . $paciente_id . '_' . time() . '.' . $extensao;
        $caminhoCompleto = $diretorioUpload . $nomeArquivoFoto;
        
        // Apaga a foto antiga antes de salvar a nova
        $stmtFotoAntiga = $conexaoBD->proteger_sql("SELECT foto_perfil FROM user_pacientes WHERE id = ?", [$paciente_id]);
        $fotoAntiga = $stmtFotoAntiga->get_result()->fetch_assoc()['foto_perfil'] ?? null;
        if ($fotoAntiga && $fotoAntiga !== 'default-paciente.png' && file_exists($diretorioUpload . $fotoAntiga)) {
            unlink($diretorioUpload . $fotoAntiga);
        }
        $stmtFotoAntiga->close();
        
        if (!move_uploaded_file($foto['tmp_name'], $caminhoCompleto)) {
            throw new Exception('Erro ao salvar a nova foto de perfil.');
        }
    }

    // Monta a query de atualização do banco
    $sql = "UPDATE user_pacientes SET email = ?, telefone = ?, data_nascimento = ?, endereco = ?";
    $params = array_values($dados);

    if ($nomeArquivoFoto) {
        $sql .= ", foto_perfil = ?";
        $params[] = $nomeArquivoFoto;
    }

    $sql .= " WHERE id = ?";
    $params[] = $paciente_id;

    // Executa a atualização
    $stmt = $conexaoBD->proteger_sql($sql, $params);
    $stmt->close();

    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Perfil atualizado com sucesso!'];

} catch (Exception $e) {
    error_log("Erro ao atualizar perfil do paciente: " . $e->getMessage());
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $e->getMessage()];
}

header('Location: configuracao_paciente.php');
exit;