<?php
// Define que a resposta será sempre no formato JSON
header('Content-Type: application/json');

// Inclui seus arquivos de inicialização e segurança
require_once 'iniciar.php';

// Apenas um médico logado pode fazer esta busca
$seguranca->proteger_pagina('medico');

// Resposta padrão em caso de erro
$response = ['success' => false, 'message' => 'Ocorreu um erro inesperado.'];

try {
    // 1. Validar o método da requisição
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido.');
    }

    // 2. Validar o CPF recebido
    $cpf = $_POST['cpf'] ?? '';
    if (empty($cpf)) {
        throw new Exception('CPF não fornecido.');
    }

    // 3. Buscar o paciente no banco de dados de forma segura
    // Selecionamos todas as colunas que o seu JS precisa para preencher o formulário
    $sql = "SELECT 
                id, 
                nome_paciente as nome, 
                cpf, 
                data_nascimento, 
                genero, 
                convenio, 
                telefone 
            FROM user_pacientes 
            WHERE cpf = ? 
            LIMIT 1";
            
    $stmt = $conexaoBD->proteger_sql($sql, [$cpf]);
    $paciente = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 4. Montar a resposta JSON
    if ($paciente) {
        // Paciente encontrado com sucesso
        $response = ['success' => true, 'paciente' => $paciente];
    } else {
        // Paciente não encontrado
        $response = ['success' => false, 'message' => 'Paciente não encontrado.'];
    }

} catch (Exception $e) {
    // Captura qualquer erro e prepara uma mensagem segura
    error_log("Erro em buscar_paciente.php: " . $e->getMessage());
    $response['message'] = 'Erro no servidor ao buscar paciente.';
}

// 5. Enviar a resposta final e encerrar o script
echo json_encode($response);
exit;