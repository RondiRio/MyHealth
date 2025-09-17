<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');

// Valida o método da requisição e o token CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: ../medico/consulta.php');
    exit;
}

// Inicia a transação. Se algo der errado, nada será salvo.
$db = $conexaoBD->getConexao();
$db->begin_transaction();

try {
    // === 1. DADOS E CADASTRO/BUSCA DO PACIENTE ===
    $cpf_paciente = $_POST['cpf_paciente'] ?? '';
    $nome_paciente = $_POST['nome_paciente'] ?? '';
    $data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
    $genero = $_POST['genero'] ?? null;
    $data_consulta = $_POST['data_consulta'] ?? '';

    if (empty($cpf_paciente) || empty($nome_paciente) || empty($data_consulta)) {
        throw new Exception('Nome, CPF do paciente e Data da consulta são obrigatórios.');
    }

    $stmtPaciente = $conexaoBD->proteger_sql("SELECT id FROM user_pacientes WHERE cpf = ? LIMIT 1", [$cpf_paciente]);
    $paciente = $stmtPaciente->get_result()->fetch_assoc();
    $stmtPaciente->close();
    
    $id_paciente = $paciente['id'] ?? null;
    
    // Se o paciente não existir, cria um novo cadastro básico
    if (!$id_paciente) {
        $senha_padrao = password_hash('senha_temporaria', PASSWORD_DEFAULT);
        $stmtNovoPaciente = $conexaoBD->proteger_sql(
            "INSERT INTO user_pacientes (nome_paciente, cpf, data_nascimento, genero, senha) VALUES (?, ?, ?, ?, ?)",
            [$nome_paciente, $cpf_paciente, $data_nascimento, $genero, $senha_padrao]
        );
        if ($stmtNovoPaciente->affected_rows === 0) {
            throw new Exception('Falha ao cadastrar novo paciente.');
        }
        $id_paciente = $stmtNovoPaciente->insert_id;
        $stmtNovoPaciente->close();
    }

    // === 2. INSERIR DADOS NA TABELA PRINCIPAL 'consultas' COM TODAS AS COLUNAS ===
    $sqlConsulta = "INSERT INTO consultas (
                        id_medico, paciente_id, cpf_paciente, nome_paciente, data_nascimento, genero, convenio, 
                        tipo_consulta, data_consulta, hipotese_diagnostica, diagnostico_final, cid_10, 
                        diagnosticos_secundarios, exames_solicitados, tratamento_proposto, orientacoes_paciente, 
                        encaminhamentos, data_retorno, prognostico, observacoes_privadas, visivel_para_paciente, 
                        consulta_urgencia
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $paramsConsulta = [
        $_SESSION['usuario_id'],
        $id_paciente,
        $cpf_paciente,
        $nome_paciente,
        $data_nascimento,
        $genero,
        $_POST['convenio'] ?? null,
        $_POST['tipo_consulta'] ?? null,
        $data_consulta,
        $_POST['hipotese_diagnostica'] ?? null,
        $_POST['diagnostico_final'] ?? null,
        $_POST['cid_10'] ?? null,
        $_POST['diagnosticos_secundarios'] ?? null,
        $_POST['exames_solicitados'] ?? null,
        $_POST['tratamento_proposto'] ?? null, // Campo agora na tabela principal
        $_POST['orientacoes_paciente'] ?? null,
        $_POST['encaminhamentos'] ?? null,
        !empty($_POST['data_retorno']) ? $_POST['data_retorno'] : null,
        $_POST['prognostico'] ?? null,
        $_POST['observacoes_privadas'] ?? null,
        isset($_POST['visivel_para_paciente']) ? 1 : 0,
        isset($_POST['consulta_urgencia']) ? 1 : 0
    ];
    $stmtConsulta = $conexaoBD->proteger_sql($sqlConsulta, $paramsConsulta);
    $id_consulta = $stmtConsulta->insert_id;
    $stmtConsulta->close();

    if ($id_consulta === 0) {
        throw new Exception('Falha ao criar o registro principal da consulta.');
    }

    // === 3. INSERIR DADOS NAS NOVAS TABELAS DE APOIO ===

    // Tabela: consultas_anamnese
    $sqlAnamnese = "INSERT INTO consultas_anamnese (id_consulta, queixa_principal_hda, historico_patologico, historico_familiar, alergias_medicamentos_uso, habitos_vida, revisao_sistemas) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $paramsAnamnese = [$id_consulta, $_POST['anamnese'] ?? null, $_POST['historico_patologico'] ?? null, $_POST['historico_familiar'] ?? null, $_POST['alergias_medicamentos'] ?? null, $_POST['habitos_vida'] ?? null, $_POST['revisao_sistemas'] ?? null];
    $conexaoBD->proteger_sql($sqlAnamnese, $paramsAnamnese)->close();

    // Tabela: consultas_exame_fisico
    $sqlExame = "INSERT INTO consultas_exame_fisico (id_consulta, estado_geral, cabeca_pescoco, aparelho_cardiovascular, aparelho_respiratorio, abdome, extremidades, sistema_neurologico, resumo_exame_fisico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $paramsExame = [$id_consulta, $_POST['estado_geral'] ?? null, $_POST['cabeca_pescoco'] ?? null, $_POST['aparelho_cardiovascular'] ?? null, $_POST['aparelho_respiratorio'] ?? null, $_POST['abdome'] ?? null, $_POST['extremidades'] ?? null, $_POST['sistema_neurologico'] ?? null, $_POST['exame_fisico'] ?? null];
    $conexaoBD->proteger_sql($sqlExame, $paramsExame)->close();

    // === 4. INSERIR SINAIS VITAIS NA TABELA 'sinais_vitais' (COM id_consulta) ===
    $sinais = $_POST['sinais_vitais'] ?? [];
    foreach($sinais as $tipo => $valor) {
        if (!empty($valor)) {
            // Separa valor e unidade (ex: "120/80 mmHg" -> valor1="120/80", unidade="mmHg")
            preg_match('/([0-9.,\/]+)\s*(.*)/', $valor, $matches);
            $valorNumerico = $matches[1] ?? $valor;
            $unidade = trim($matches[2] ?? '');
            
            $sqlSinais = "INSERT INTO sinais_vitais (id_paciente, id_consulta, tipo, valor1, unidade) VALUES (?, ?, ?, ?, ?)";
            $paramsSinais = [$id_paciente, $id_consulta, $tipo, $valorNumerico, $unidade];
            $conexaoBD->proteger_sql($sqlSinais, $paramsSinais)->close();
        }
    }

    // === 5. PROCESSAR UPLOAD DE ARQUIVOS (usando a tabela documentos_paciente) ===
    if (isset($_FILES['documentos']) && !empty($_FILES['documentos']['name'][0])) {
        $uploadDir = 'uploads/consultas_anexos/';
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        
        foreach ($_FILES['documentos']['name'] as $key => $name) {
            if ($_FILES['documentos']['error'][$key] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['documentos']['tmp_name'][$key];
                $safe_name = uniqid('consulta'.$id_consulta.'_') . '_' . basename($name);
                $caminho_arquivo = $uploadDir . $safe_name;

                if (move_uploaded_file($tmp_name, $caminho_arquivo)) {
                    $sqlAnexo = "INSERT INTO documentos_paciente (id_paciente, id_consulta, titulo_documento, nome_arquivo, caminho_arquivo, observacoes) VALUES (?, ?, ?, ?, ?, ?)";
                    $paramsAnexo = [$id_paciente, $id_consulta, $name, $safe_name, $caminho_arquivo, $_POST['descricao_anexos'] ?? ''];
                    $conexaoBD->proteger_sql($sqlAnexo, $paramsAnexo)->close();
                }
            }
        }
    }

    // Se tudo deu certo, confirma as alterações no banco
    $db->commit();
    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Consulta registrada com sucesso!'];

} catch (Exception $e) {
    // Se algo deu errado, desfaz todas as alterações
    $db->rollback();
    error_log("Erro ao registrar consulta: " . $e->getMessage());
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Erro ao salvar consulta: ' . $e->getMessage()];
}

header('Location: ../medico/consulta.php');
exit;