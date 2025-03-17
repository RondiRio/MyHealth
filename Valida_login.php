<?php
include_once('bancoDeDados/sql/conexaoBD.php');

class ValidaLogin {
    private $conn;
    private $tipoUsuario;
    private $identificador;
    private $senha;

    public function __construct(ConexaoBD $conexao) {
        $this->conn = $conexao->getConexao();
    }

    public function recebeDados($tipoUsuario, $identificador, $senha) {
        $this->tipoUsuario = $tipoUsuario;
        $this->identificador = $identificador;
        $this->senha = $senha;
    }

    public function validarUsuario() {
        $query = $this->tipoUsuario === 'medico' 
            ? 'SELECT * FROM user_medicos WHERE crm = ?' 
            : 'SELECT * FROM user_pacientes WHERE cpf = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $this->identificador);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $this->verificarSenha($usuario);
        } else {
            $this->setErroLogin('Usuário não encontrado.');
        }
    }

    private function verificarSenha($usuario) {
        if (password_verify($this->senha, $usuario['senha'])) {
            $this->iniciarSessao($usuario);
        } else {
            $this->setErroLogin('Senha incorreta.');
        }
    }

    private function iniciarSessao($usuario) {
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $this->tipoUsuario;

        if ($this->tipoUsuario === 'medico') {
            $_SESSION['crm'] = $usuario['crm'];
        } elseif ($this->tipoUsuario === 'paciente') {
            $_SESSION['cpf'] = $usuario['cpf'];
        }

        $this->redirecionarDashboard();
    }

    private function redirecionarDashboard() {
        
        $dashboard = $this->tipoUsuario === 'medico' ? 'dashboard_medico.php' : 'dashboard_Paciente.php';

        if($dashboard == 'medico_dashboard.php'){
            header("Location: $dashboard");
        }
        else{
            header("Location: $dashboard");
        }
        exit();
    }

    private function setErroLogin($mensagem) {
        session_start();
        $_SESSION['erro_login'] = $mensagem;
        header('Location: naoacessou.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexao = new ConexaoBD();
    $login = new ValidaLogin($conexao);
    $login->recebeDados($_POST['tipo_usuario'], $_POST['identificador'], $_POST['senha']);
    $login->validarUsuario();
} else {
    session_start();
    $_SESSION['erro_login'] = 'Acesso inválido.';
    header('Location: naoacessou.php');
    exit();
}
?>
