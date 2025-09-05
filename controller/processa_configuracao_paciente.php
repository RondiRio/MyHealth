<?php
require_once '../controller/iniciar.php';

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
        'endereco' => $_POST['endereco'] ?? '',
        'rg' => $_POST['rg'] ?? '',
        'cartao_sus' => $_POST['cartao_sus'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'genero' => $_POST['genero'] ?? '',
        'estado_civil' => $_POST['estado_civil'] ?? '',
        'profissao' => $_POST['profissao'] ?? '',
        'convenio' => $_POST['convenio'] ?? '',
    ]);

    // Validações específicas
    if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido.');
    }

    // Validação do telefone (formato brasileiro)
    if (!empty($dados['telefone'])) {
        $telefone_limpo = preg_replace('/\D/', '', $dados['telefone']);
        if (strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
            throw new Exception('Telefone deve ter entre 10 e 11 dígitos.');
        }
    }

    // Validação do RG (formato básico)
    if (!empty($dados['rg'])) {
        $rg_limpo = preg_replace('/\D/', '', $dados['rg']);
        if (strlen($rg_limpo) < 8 || strlen($rg_limpo) > 9) {
            throw new Exception('RG deve ter entre 8 e 9 dígitos.');
        }
    }

    // Validação do Cartão SUS
    if (!empty($dados['cartao_sus'])) {
        $sus_limpo = preg_replace('/\D/', '', $dados['cartao_sus']);
        if (strlen($sus_limpo) !== 15) {
            throw new Exception('Cartão SUS deve ter exatamente 15 dígitos.');
        }
    }

    // Validação do sexo
    if (!empty($dados['sexo']) && !in_array($dados['sexo'], ['M', 'F'])) {
        throw new Exception('Sexo deve ser M ou F.');
    }

    // Validação do gênero
    $generos_validos = ['Masculino', 'Feminino', 'Não-binário', 'Outro', 'Prefiro não informar'];
    if (!empty($dados['genero']) && !in_array($dados['genero'], $generos_validos)) {
        throw new Exception('Gênero selecionado é inválido.');
    }

    // Validação do estado civil
    $estados_civis_validos = ['Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viúvo(a)', 'União Estável'];
    if (!empty($dados['estado_civil']) && !in_array($dados['estado_civil'], $estados_civis_validos)) {
        throw new Exception('Estado civil selecionado é inválido.');
    }

    // Lógica para o upload da foto de perfil
    $nomeArquivoFoto = null;
    $diretorioUpload = '../uploads/fotos_perfil/';

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto_perfil'];
        
        // Validações de segurança do arquivo
        if ($foto['size'] > 5 * 1024 * 1024) {
            throw new Exception('Erro: O arquivo de imagem é muito grande (máximo 5MB).');
        }
        
        $mimeType = mime_content_type($foto['tmp_name']);
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new Exception('Erro: Formato de arquivo não permitido (use JPG, PNG ou GIF).');
        }

        // Validação adicional: verifica se é realmente uma imagem
        $imageInfo = getimagesize($foto['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('Erro: Arquivo não é uma imagem válida.');
        }

        // Cria um nome de arquivo novo e seguro
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $nomeArquivoFoto = 'paciente_' . $paciente_id . '_' . time() . '.' . $extensao;
        $caminhoCompleto = $diretorioUpload . $nomeArquivoFoto;
        
        // Verifica se o diretório existe, se não, cria
        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }
        
        // Apaga a foto antiga antes de salvar a nova
        $stmtFotoAntiga = $conexaoBD->proteger_sql("SELECT foto_perfil FROM user_pacientes WHERE id = ?", [$paciente_id]);
        $resultadoFoto = $stmtFotoAntiga->get_result();
        $fotoAntiga = $resultadoFoto->fetch_assoc()['foto_perfil'] ?? null;
        $stmtFotoAntiga->close();
        
        if ($fotoAntiga && $fotoAntiga !== 'default-paciente.png' && file_exists($diretorioUpload . $fotoAntiga)) {
            unlink($diretorioUpload . $fotoAntiga);
        }
        
        if (!move_uploaded_file($foto['tmp_name'], $caminhoCompleto)) {
            throw new Exception('Erro ao salvar a nova foto de perfil.');
        }
    }

    // Verifica se o email já está sendo usado por outro paciente
    if (!empty($dados['email'])) {
        $stmtEmailCheck = $conexaoBD->proteger_sql(
            "SELECT id FROM user_pacientes WHERE email = ? AND id != ?", 
            [$dados['email'], $paciente_id]
        );
        $resultadoEmail = $stmtEmailCheck->get_result();
        if ($resultadoEmail->num_rows > 0) {
            $stmtEmailCheck->close();
            throw new Exception('Este email já está sendo usado por outro paciente.');
        }
        $stmtEmailCheck->close();
    }

    // Monta a query de atualização do banco
    $sql = "UPDATE user_pacientes SET 
                email = ?, 
                telefone = ?, 
                endereco = ?, 
                rg = ?, 
                cartao_sus = ?, 
                sexo = ?, 
                genero = ?, 
                estado_civil = ?, 
                profissao = ?, 
                convenio = ?";
    
    $params = [
        $dados['email'],
        $dados['telefone'],
        $dados['endereco'],
        $dados['rg'],
        $dados['cartao_sus'],
        $dados['sexo'],
        $dados['genero'],
        $dados['estado_civil'],
        $dados['profissao'],
        $dados['convenio']
    ];

    // Adiciona a foto se foi feito upload
    if ($nomeArquivoFoto) {
        $sql .= ", foto_perfil = ?";
        $params[] = $nomeArquivoFoto;
    }

    $sql .= " WHERE id = ?";
    $params[] = $paciente_id;

    // Executa a atualização
    $stmt = $conexaoBD->proteger_sql($sql, $params);
    
    if ($stmt->affected_rows >= 0) { // >= 0 porque pode ser 0 se nada mudou
        $stmt->close();
        
        // Log da atividade
        error_log("Perfil do paciente ID {$paciente_id} atualizado com sucesso.");
        
        $_SESSION['notificacao'] = [
            'tipo' => 'sucesso', 
            'mensagem' => 'Perfil atualizado com sucesso!'
        ];
    } else {
        $stmt->close();
        throw new Exception('Erro ao atualizar o perfil. Tente novamente.');
    }

} catch (Exception $e) {
    // Log do erro para debug
    error_log("Erro ao atualizar perfil do paciente ID {$paciente_id}: " . $e->getMessage());
    
    // Remove arquivo de foto se foi feito upload mas houve erro
    if (isset($nomeArquivoFoto) && $nomeArquivoFoto && file_exists($diretorioUpload . $nomeArquivoFoto)) {
        unlink($diretorioUpload . $nomeArquivoFoto);
    }
    
    $_SESSION['notificacao'] = [
        'tipo' => 'erro', 
        'mensagem' => $e->getMessage()
    ];
}

header('Location: ../paciente/configuracao_paciente.php');
exit;