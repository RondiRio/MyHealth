<?php
// 1. FUNDAÇÃO E SEGURANÇA
// Carrega nosso ambiente seguro e as ferramentas de banco de dados.
require_once 'iniciar.php';
// Garante que apenas um médico logado pode acessar este recurso.
$seguranca->proteger_pagina('medico');

// Define o cabeçalho para indicar que a resposta será em formato JSON.
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

    // 3. BUSCA SEPARADA: PRIMEIRO, OS DADOS DO PACIENTE
    $sqlPaciente = "SELECT nome, cpf, email, telefone FROM user_pacientes WHERE cpf = ?";
    $stmtPaciente = $conexaoBD->proteger_sql($sqlPaciente, [$cpf]);
    $resultadoPaciente = $stmtPaciente->get_result();
    $dadosPaciente = $resultadoPaciente->fetch_assoc();
    $stmtPaciente->close();

    if (!$dadosPaciente) {
        // Se não encontrou o paciente, retorna erro.
        echo json_encode(['success' => false, 'message' => 'Paciente não encontrado.']);
        exit;
    }

    // 4. BUSCA SEPARADA: SEGUNDO, AS CONSULTAS DO PACIENTE
    $sqlConsultas = "SELECT c.*, m.nome as nome_medico FROM consultas c
                      JOIN user_medicos m ON c.id_medico = m.id
                      WHERE c.cpf_paciente = ?
                      ORDER BY c.data_consulta DESC";

    $stmtConsultas = $conexaoBD->proteger_sql($sqlConsultas, [$cpf]);
    $resultadoConsultas = $stmtConsultas->get_result();
    $consultas = $resultadoConsultas->fetch_all(MYSQLI_ASSOC);
    $stmtConsultas->close();

    // 5. PREPARAR E ENVIAR A RESPOSTA JSON FINAL
    echo json_encode([
        'success' => true,
        'paciente' => $dadosPaciente,
        'consultas' => $consultas
    ]);

} catch (Exception $e) {
    // Em caso de qualquer erro, captura a exceção e retorna uma resposta de erro em JSON.
    http_response_code(400); // Define o status HTTP para "Bad Request"
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>