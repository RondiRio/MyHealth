<?php
include('../bancoDeDados/sql/conexaoBD.php'); 
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $diagnostico = filter_input(INPUT_POST, 'diagnostico', FILTER_SANITIZE_STRING);

    if (!$id || !$nome || !$cpf || !$diagnostico) {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
        exit;
    }

    try {
        $conexao = new ConexaoBD();
        $conn = $conexao->getConexao();

        $sql = "UPDATE consultas_realizadas SET nome_paciente = ?, cpf_paciente = ?, diagnostico = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $nome, $cpf, $diagnostico, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
