<?php
include('bancoDeDados/sql/conexaoBD.php');
session_start();

class Consulta {
    private $conn;
    private $medicoId;

    public function __construct($conn, $medicoId) {
        $this->conn = $conn;
        $this->medicoId = $medicoId;
    }

    public function buscarIdPaciente($cpfPaciente) {
        $sql = "SELECT id FROM user_pacientes WHERE cpf = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $cpfPaciente);
        $stmt->execute();
        $result = $stmt->get_result();
        $paciente = $result->fetch_assoc();

        if (!$paciente) {
            throw new Exception("Erro: Paciente não encontrado.");
        }

        return $paciente['id'];
    }

    public function verificarHospital($hospital_id) {
        $sql = "SELECT id FROM hospitais WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $hospital = $result->fetch_assoc();

        if (!$hospital) {
            throw new Exception("Erro: O hospital especificado não existe no sistema.");
        }
    }

    public function registrarConsulta($cpfPaciente, $dataConsulta, $hospital_id) {
        $id_paciente = $this->buscarIdPaciente($cpfPaciente);

        // Verifica se o hospital existe antes de registrar a consulta
        $this->verificarHospital($hospital_id);

        $sql = "INSERT INTO consultas_realizadas (medico_id, paciente_id, hospital_id, cpf_paciente, ultima_consulta) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->conn->error);
        }

        $stmt->bind_param("iiiss", $this->medicoId, $id_paciente, $hospital_id, $cpfPaciente, $dataConsulta);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Consulta registrada com sucesso!";
        } else {
            throw new Exception("Erro ao registrar a consulta.");
        }
    }
}

$conexao = new ConexaoBD();
$conn = $conexao->getConexao();

if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Sessão inválida. Faça login novamente.");
}

$medicoId = $_SESSION['usuario_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['cpf']) || !isset($_POST['data']) || !isset($_POST['hospital_id'])) {
            throw new Exception("Erro: Dados incompletos para registrar a consulta.");
        }

        $cpfPaciente = $_POST['cpf'];
        $dataConsulta = $_POST['data'];
        $hospital_id = $_POST['hospital_id'];

        $consulta = new Consulta($conn, $medicoId);
        $consulta->registrarConsulta($cpfPaciente, $dataConsulta, $hospital_id);

        header("Location: lista_pacientes.php");
        exit();
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
