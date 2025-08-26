<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once 'bancoDeDados/sql/conexaoBD.php'; 
require_once 'Paciente.php';
var_dump('Token POST:', $_POST['csrf_token'] ?? 'Nenhum');
var_dump('Token SESSÃO:', $_SESSION['csrf_token'] ?? 'Nenhum');
function send_response(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, ['success' => false, 'message' => 'Método não permitido.']);
}

session_start();

// Para testes, vamos fixar o ID do paciente. Em produção, você pegaria da sessão.
$_SESSION['paciente_id'] = 1;

if (!isset($_SESSION['paciente_id'])) {
    send_response(403, ['success' => false, 'message' => 'Acesso não autorizado.']);
}
// O token CSRF deve ser implementado como no exemplo anterior.

try {
    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();
    $paciente = new Paciente($conn, (int)$_SESSION['paciente_id']);

    $action = $_POST['action'] ?? '';
    if (empty($action)) {
        send_response(400, ['success' => false, 'message' => 'Ação não especificada.']);
    }

    switch ($action) {
        case 'add_allergy':
            $success = $paciente->addAllergy($_POST['tipo'], $_POST['nomeAgente'], $_POST['sintomas']);
            if ($success) send_response(201, ['success' => true, 'message' => 'Alergia adicionada.']);
            else send_response(500, ['success' => false, 'message' => 'Erro ao adicionar alergia.']);
            break;

        case 'delete_allergy':
            $allergyId = (int)($_POST['allergy_id'] ?? 0);
            if ($allergyId > 0) {
                $success = $paciente->deleteAllergy($allergyId);
                if ($success) send_response(200, ['success' => true, 'message' => 'Alergia removida.']);
                else send_response(500, ['success' => false, 'message' => 'Erro ao remover alergia.']);
            } else {
                send_response(400, ['success' => false, 'message' => 'ID da alergia inválido.']);
            }
            break;
            
        case 'register_vital_sign':
            $success = $paciente->addVitalSign($_POST['tipo'], $_POST['valor1'], $_POST['valor2'] ?? null, $_POST['unidade'], $_POST['observacoes']);
            if ($success) send_response(201, ['success' => true, 'message' => 'Sinal vital registrado.']);
            else send_response(500, ['success' => false, 'message' => 'Erro ao registrar sinal vital.']);
            break;

        case 'upload_exam':
            if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                $success = $paciente->uploadExam($_POST['nomeDocumento'], $_POST['dataExame'], $_POST['observacoes'], $_FILES['arquivo']);
                if ($success) send_response(201, ['success' => true, 'message' => 'Exame enviado com sucesso.']);
                else send_response(500, ['success' => false, 'message' => 'Erro ao salvar o exame.']);
            } else {
                send_response(400, ['success' => false, 'message' => 'Erro no upload do arquivo.']);
            }
            break;

        default:
            send_response(400, ['success' => false, 'message' => 'Ação desconhecida.']);
            break;
    }
} catch (Exception $e) {
    // Em produção, logar o erro: error_log($e->getMessage());
    send_response(500, ['success' => false, 'message' => 'Ocorreu um erro inesperado no servidor.']);
}
?>