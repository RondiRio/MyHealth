<?php
// 1. FUNDAÇÃO com DEBUG
require_once 'iniciar.php';

// DEBUG: Log da requisição
error_log("LOGIN ATTEMPT - Method: " . $_SERVER['REQUEST_METHOD'] . " | CSRF Token: " . ($_POST['csrf_token'] ?? 'MISSING'));

// Verificação CSRF com debug
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("LOGIN ERROR: Método não é POST");
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Método de requisição inválido.'];
    header('Location: index.php');
    exit;
}

if (!$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    error_log("LOGIN ERROR: Token CSRF inválido");
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Formulário expirado. Tente novamente.'];
    header('Location: index.php');
    exit;
}

class ValidaLogin {
    private $conexaoBD;
    private $seguranca;

    public function __construct(ConexaoBD $conexaoBD, Seguranca $seguranca) {
        $this->conexaoBD = $conexaoBD;
        $this->seguranca = $seguranca;
    }

    public function validarUsuario(string $tipoUsuario, string $identificador, string $senha) {
        // DEBUG: Log dos dados recebidos
        error_log("LOGIN DEBUG - Tipo: $tipoUsuario | Identificador: $identificador | Senha length: " . strlen($senha));
        
        $tabela = ($tipoUsuario === 'medico') ? 'user_medicos' : 'user_pacientes';
        $coluna = ($tipoUsuario === 'medico') ? 'crm' : 'cpf';
        
        error_log("LOGIN DEBUG - Tabela: $tabela | Coluna: $coluna");
        
        $sql = "SELECT * FROM $tabela WHERE $coluna = ?";
        $stmt = $this->conexaoBD->proteger_sql($sql, [$identificador]);
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // DEBUG: Verifica se encontrou o usuário
        if (!$usuario) {
            error_log("LOGIN ERROR: Usuário não encontrado na tabela $tabela com $coluna = $identificador");
            $this->setErroLogin("$tipoUsuario não encontrado com este " . ($tipoUsuario === 'medico' ? 'CRM' : 'CPF') . ".");
            return;
        }

        error_log("LOGIN DEBUG - Usuário encontrado: ID " . $usuario['id']);

        // DEBUG: Verifica senha
        if (!password_verify($senha, $usuario['senha'])) {
            error_log("LOGIN ERROR: Senha incorreta para usuário ID " . $usuario['id']);
            $this->setErroLogin('Senha incorreta.');
            return;
        }

        error_log("LOGIN SUCCESS: Login válido para usuário ID " . $usuario['id']);
        $this->iniciarSessaoCompleta($usuario, $tipoUsuario);
    }

    private function iniciarSessaoCompleta(array $usuario, string $tipoUsuario) {
        error_log("LOGIN DEBUG - Iniciando sessão completa para usuário ID " . $usuario['id']);
        
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['tipo_usuario'] = $tipoUsuario;
        $_SESSION['user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['last_activity'] = time();
        $_SESSION['usuario_nome'] = $usuario['nome'] ?? '';
        
        if ($tipoUsuario === 'medico') {
            $_SESSION['crm'] = $usuario['crm'];
        }

        session_write_close();

        $dashboard = ($tipoUsuario === 'medico') ? 'dashboard_medico.php' : 'dashBoard_Paciente.php';
        error_log("LOGIN SUCCESS - Redirecionando para: $dashboard");
        
        header("Location: " . $dashboard);
        exit();
    }

    private function setErroLogin($mensagem) {
        error_log("LOGIN ERROR - Mensagem: $mensagem");
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: login.php');
        exit();
    }
}

// 3. EXECUÇÃO com validação adicional
$tipo_usuario = $seguranca->sanitizar_entrada($_POST['tipo_usuario'] ?? '');
$identificador = $seguranca->sanitizar_entrada($_POST['identificador'] ?? '');
$senha = $_POST['senha'] ?? '';

// DEBUG: Validação dos campos obrigatórios
if (empty($tipo_usuario) || empty($identificador) || empty($senha)) {
    error_log("LOGIN ERROR: Campos obrigatórios vazios");
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Todos os campos são obrigatórios.'];
    header('Location: index.php');
    exit;
}

if (!in_array($tipo_usuario, ['medico', 'paciente'])) {
    error_log("LOGIN ERROR: Tipo de usuário inválido: $tipo_usuario");
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Tipo de usuário inválido.'];
    header('Location: index.php');
    exit;
}

$login = new ValidaLogin($conexaoBD, $seguranca);
$login->validarUsuario($tipo_usuario, $identificador, $senha);
?>