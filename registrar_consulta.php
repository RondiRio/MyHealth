<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');

// Valida o método da requisição e o token CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: consulta.php');
    exit;
}

try {
    // Receber os dados do formulário diretamente
    $cpf_paciente = $_POST['cpf_paciente'] ?? '';
    $nome_paciente = $_POST['nome_paciente'] ?? '';
    $data_consulta = $_POST['data_consulta'] ?? '';
    
    $anamnese = $_POST['anamnese'] ?? '';
    $exame_fisico = $_POST['exame_fisico'] ?? '';
    $diagnostico_final = $_POST['diagnostico_final'] ?? '';
    $tratamento_proposto = $_POST['tratamento_proposto'] ?? '';
    $observacoes_privadas = $_POST['observacoes_privadas'] ?? '';
    $visivel_para_paciente = isset($_POST['visivel_para_paciente']) && $_POST['visivel_para_paciente'] === '1' ? 1 : 0;

    // Validação de campos obrigatórios
    if (empty($cpf_paciente) || empty($nome_paciente) || empty($data_consulta)) {
        throw new Exception('Nome, CPF do paciente e Data da consulta são obrigatórios.');
    }

    // Verificar se o paciente tem cadastro para vincular o ID (opcional)
    $stmtPaciente = $conexaoBD->proteger_sql("SELECT id FROM user_pacientes WHERE cpf = ? LIMIT 1", [$cpf_paciente]);
    $paciente = $stmtPaciente->get_result()->fetch_assoc();
    $stmtPaciente->close();

    $id_paciente = $paciente ? $paciente['id'] : null;
    $id_medico = $_SESSION['usuario_id'];

    $sql = "INSERT INTO consultas (
                id_medico, paciente_id, cpf_paciente, nome_paciente, data_consulta, anamnese, exame_fisico, 
                diagnostico_final, tratamento_proposto, observacoes_privadas, visivel_para_paciente
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $id_medico,
        $id_paciente,
        $cpf_paciente,
        $nome_paciente,
        $data_consulta,
        $anamnese,
        $exame_fisico,
        $diagnostico_final,
        $tratamento_proposto,
        $observacoes_privadas,
        $visivel_para_paciente
    ];

    $stmt = $conexaoBD->proteger_sql($sql, $params);

    if ($stmt->affected_rows > 0) {
        $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Consulta registrada com sucesso no prontuário!'];
    } else {
        throw new Exception('Não foi possível salvar a consulta. Tente novamente.');
    }
    $stmt->close();

} catch (Exception $e) {
    error_log("Erro ao registrar consulta: " . $e->getMessage());
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $e->getMessage()];
}

header('Location: consulta.php');
exit;
