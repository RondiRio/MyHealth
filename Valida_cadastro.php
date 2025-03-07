<?php
session_start();
include_once('bancoDeDados/sql/conexaoBD.php');

class ValidaCadastro {
    private $conn;
    private $email;
    private $senha;
    private $confirmarSenha;
    private $tipoUsuario;
    private $identificador;

    public function __construct(ConexaoBD $conexao) {
        $this->conn = $conexao->getConexao();
    }

    public function recebeDados($email, $senha, $confirmarSenha, $tipoUsuario, $identificador) {
        $this->email = trim($email);
        $this->senha = trim($senha);
        $this->confirmarSenha = trim($confirmarSenha);
        $this->tipoUsuario = $tipoUsuario;
        $this->identificador = trim($identificador);
    }

    public function validarCadastro() {
        if ($this->camposVazios()) {
            $this->setErro('Preencha todos os campos!');
        }

        if ($this->senha !== $this->confirmarSenha) {
            $this->setErro('As senhas não coincidem!');
        }

        $this->verificarUsuario();
    }

    private function camposVazios() {
        return empty($this->email) || empty($this->senha) || empty($this->confirmarSenha);
    }

    private function verificarUsuario() {
        $tabela = $this->tipoUsuario === 'medico' ? 'user_medicos' : 'user_pacientes';
        $colunaIdentificador = $this->tipoUsuario === 'medico' ? 'crm' : 'cpf';

        $sql = "SELECT id FROM $tabela WHERE $colunaIdentificador = ? OR email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $this->identificador, $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $this->setErro('Usuário já cadastrado!');
        }

        $this->cadastrarUsuario($tabela, $colunaIdentificador);
    }

    private function cadastrarUsuario($tabela, $colunaIdentificador) {
        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO $tabela (email, senha, $colunaIdentificador) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $this->email, $senhaHash, $this->identificador);

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = 'Cadastro realizado com sucesso! Faça login.';
            header('Location: login.php');
            exit();
        } else {
            $this->setErro('Erro ao cadastrar. Tente novamente!');
        }
    }

    private function setErro($mensagem) {
        $_SESSION['erro'] = $mensagem;
        header('Location: cadastrar.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexao = new ConexaoBD();
    $cadastro = new ValidaCadastro($conexao);
    $cadastro->recebeDados($_POST['email'], $_POST['senha'], $_POST['confirmar_senha'], $_POST['tipo_usuario'], $_POST['identificador']);
    $cadastro->validarCadastro();
} else {
    $_SESSION['erro'] = 'Acesso inválido.';
    header('Location: cadastrar.php');
    exit();
}
?>
