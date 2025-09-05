<?php
session_start();
require_once '../bancoDeDados/sql/conexaoBD.php';

class Medico {
    private $conn;
    private $nome;
    private $especialidade;
    private $email;
    private $telefone;
    private $endereco;
    private $cidade;
    private $estado;
    private $data_nascimento;
    private $genero;
    private $ano_formacao;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function setDados($dados) {
        $this->nome = trim($dados['nome']);
        $this->especialidade = trim($dados['especialidade']);
        $this->email = trim($dados['email']);
        $this->telefone = trim($dados['telefone']);
        $this->endereco = trim($dados['endereco']);
        $this->cidade = trim($dados['cidade']);
        $this->estado = trim($dados['estado']);
        $this->data_nascimento = $dados['data_nascimento'];
        $this->genero = $dados['genero'];
        $this->ano_formacao = $dados['ano_formacao'];
    }

 
    public function emailExiste() {
        $query = "SELECT id FROM user_medicos WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function atualizar() {
        $query = "UPDATE user_medicos SET 
                    nome = ?, 
                    especialidade = ?, 
                    telefone = ?, 
                    endereco_medico = ?, 
                    cidade = ?, 
                    estado = ?, 
                    data_nascimento = ?, 
                    genero = ?,
                    ano_formacao = ?
                  WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssssss", $this->nome, $this->especialidade, $this->telefone, $this->endereco, $this->cidade, $this->estado, $this->data_nascimento, $this->genero, $this->email, $this->ano_formacao);

        if (!$stmt->execute()) {
            throw new Exception('Erro ao atualizar dados: ' . $stmt->error);
        }
        $stmt->close();
    }
}

try {
    $conn = (new ConexaoBD())->getConexao();
    $medico = new Medico($conn);
    $medico->setDados($_POST);
    // $medico->validarDados();

    if ($medico->emailExiste()) {
        $medico->atualizar();
        echo 'Dados atualizados com sucesso!';
    } else {
        echo 'Erro: E-mail não encontrado.';
    }

    header('Location: dashboard_medico.php');
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}

$conn->close();
?>
