<?php
include('bancoDeDados/sql/conexaoBD.php');
session_start();

class Hospital {
    private $conn;
    private $medico_id;

    public function __construct($conn, $medico_id) {
        $this->conn = $conn;
        $this->medico_id = $medico_id;
    }

    public function verificarHospital($cnpj) {
        $sql = "SELECT id FROM hospitais WHERE cnpj = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $cnpj);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retorna o ID do hospital se já existir
    }

    public function vincularMedicoHospital($hospital_id) {
        // Verifica se o médico já está vinculado ao hospital
        $sql = "SELECT id FROM medicos_hospitais WHERE medico_id = ? AND hospital_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->medico_id, $hospital_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Se não estiver vinculado, faz a inserção
            $sql = "INSERT INTO medicos_hospitais (medico_id, hospital_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $this->medico_id, $hospital_id);
            $stmt->execute();
        }
    }

    public function cadastrarNovoHospital($dados) {
        $sql = "INSERT INTO hospitais (nome, cnpj, telefone, endereco, cidade, estado, medico_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $dados['nome'], $dados['cnpj'], $dados['telefone'], 
            $dados['endereco'], $dados['cidade'], $dados['estado'], 
            $this->medico_id
        );

        if ($stmt->execute()) {
            return $stmt->insert_id; // Retorna o ID do novo hospital
        } else {
            throw new Exception("Erro ao cadastrar hospital.");
        }
    }

    public function processarCadastro($dados) {
        // Vincular hospitais existentes
        if (!empty($dados['hospital_id'])) {
            foreach ($dados['hospital_id'] as $hospital_id) {
                $this->vincularMedicoHospital($hospital_id);
            }
        }

        // Se foi informado um CNPJ, verificar se o hospital já existe
        if (!empty($dados['cnpj'])) {
            $hospitalExistente = $this->verificarHospital($dados['cnpj']);

            if ($hospitalExistente) {
                $this->vincularMedicoHospital($hospitalExistente['id']);
                echo "Hospital já cadastrado. Médico vinculado!";
            } else {
                // Se não existir, cadastrar e vincular
                $novo_hospital_id = $this->cadastrarNovoHospital($dados);
                $this->vincularMedicoHospital($novo_hospital_id);
                echo "Hospital cadastrado e médico vinculado com sucesso!";
            }
        }
    }
}

$conexao = new ConexaoBD();
$conn = $conexao->getConexao();

if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Sessão inválida. Faça login novamente.");
}

$medico_id = $_SESSION['usuario_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hospitalObj = new Hospital($conn, $medico_id);
        $hospitalObj->processarCadastro($_POST);

        header("Location: dashboard_medico.php");
        exit();
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
