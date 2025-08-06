<?php
// 1. FUNDAÇÃO E SEGURANÇA
// Carrega nosso ambiente seguro e as ferramentas de banco de dados.
require_once 'iniciar.php';
// Garante que apenas um médico logado pode acessar este recurso.
$seguranca->proteger_pagina('medico');

// Define o cabeçalho para indicar que a resposta será em formato JSON.
header('Content-Type: application/json');

try {
    // Valida se a requisição foi feita usando o método POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // 2. RECEBER E SANITIZAR O CPF
    $cpf = $seguranca->sanitizar_entrada($_POST['cpf'] ?? '');
    if (empty($cpf)) {
        throw new Exception('O CPF é obrigatório para a busca.');
    }

    // 3. NOVA LÓGICA DE BUSCA ALINHADA COM O BANCO DE DADOS ATUAL
    // A consulta agora busca todos os campos necessários da tabela `consultas`.
    $sql = "SELECT
                c.*, -- Pega todas as colunas da tabela de consultas
                m.nome as nome_medico
            FROM consultas c
            JOIN user_medicos m ON c.id_medico = m.id
            WHERE c.cpf_paciente = ?
            ORDER BY c.data_consulta DESC";

    $stmt = $conexaoBD->proteger_sql($sql, [$cpf]);
    $resultado = $stmt->get_result();
    $consultas = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 4. PREPARAR E ENVIAR A RESPOSTA JSON CORRETA
    if (count($consultas) > 0) {
        // Se encontrou consultas, os dados básicos do paciente (nome e CPF)
        // são pegos do primeiro registro encontrado.
        $dadosPaciente = [
            'nome' => $consultas[0]['nome_paciente'],
            'cpf' => $consultas[0]['cpf_paciente']
        ];

        // Envia uma resposta de sucesso com os dados do paciente e a lista de consultas.
        echo json_encode([
            'success' => true,
            'paciente' => $dadosPaciente,
            'consultas' => $consultas
        ]);
    } else {
        // Se não encontrou nenhuma consulta para o CPF informado.
        echo json_encode(['success' => false, 'message' => 'Nenhum histórico de consulta encontrado para este CPF.']);
    }

} catch (Exception $e) {
    // Em caso de qualquer erro, captura a exceção e retorna uma resposta de erro em JSON.
    http_response_code(400); // Define o status HTTP para "Bad Request"
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
