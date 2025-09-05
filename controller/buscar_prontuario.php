<?php
// 1. FUNDAÇÃO E SEGURANÇA
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // 2. RECEBER E SANITIZAR O CPF
    $cpf = $seguranca->sanitizar_entrada($_POST['cpf'] ?? '');
    if (empty($cpf)) {
        throw new Exception('O CPF é obrigatório para a busca.');
    }

    // 3. BUSCAR DADOS COMPLETOS DO PACIENTE
    // Esta consulta busca todos os novos campos que adicionamos à tabela user_pacientes.
    $stmtPaciente = $conexaoBD->proteger_sql("SELECT * FROM user_pacientes WHERE cpf = ? LIMIT 1", [$cpf]);
    $paciente = $stmtPaciente->get_result()->fetch_assoc();
    $stmtPaciente->close();

    // Se não encontrar paciente com cadastro, busca por consultas avulsas.
    if (!$paciente) {
        $stmtNomeAvulso = $conexaoBD->proteger_sql("SELECT nome_paciente FROM consultas WHERE cpf_paciente = ? ORDER BY data_consulta DESC LIMIT 1", [$cpf]);
        $nomeAvulso = $stmtNomeAvulso->get_result()->fetch_assoc();
        $stmtNomeAvulso->close();

        if (!$nomeAvulso) {
            throw new Exception('Nenhum registro encontrado para o CPF informado.');
        }
        // Cria um objeto de paciente "mínimo" para a resposta.
        $paciente = ['id' => null, 'nome' => $nomeAvulso['nome_paciente'], 'cpf' => $cpf];
    }

    $paciente_id = $paciente['id'];

    // 4. BUSCAR O HISTÓRICO DE CONSULTAS
    $sqlConsultas = "SELECT c.*, m.nome as nome_medico 
                     FROM consultas c
                     JOIN user_medicos m ON c.id_medico = m.id
                     WHERE c.cpf_paciente = ? 
                     ORDER BY c.data_consulta DESC";
    $stmtConsultas = $conexaoBD->proteger_sql($sqlConsultas, [$cpf]);
    $consultas = $stmtConsultas->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtConsultas->close();

    // 5. BUSCAR OS DOCUMENTOS ANEXADOS (APENAS SE O PACIENTE TIVER CADASTRO)
    $documentos = [];
    if ($paciente_id) {
        $stmtDocumentos = $conexaoBD->proteger_sql("SELECT * FROM documentos_paciente WHERE id_paciente = ? ORDER BY data_upload DESC", [$paciente_id]);
        $documentos = $stmtDocumentos->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmtDocumentos->close();
    }

    // 6. MONTAR E ENVIAR A RESPOSTA JSON COMPLETA
    echo json_encode([
        'success' => true,
        'paciente' => $paciente,
        'consultas' => $consultas,
        'documentos' => $documentos
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
