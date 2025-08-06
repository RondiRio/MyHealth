<?php
// 1. FUNDAÇÃO
// Usamos o 'iniciar.php' que já lida com a sessão e nos dá as ferramentas.
require_once 'iniciar.php';

// Verificamos o CSRF e o método POST aqui fora da classe.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: index.php'); // Redireciona para o login na index
    exit;
}

class ValidaLogin {
    private $conexaoBD;
    private $seguranca;

    // Construtor agora recebe as dependências do nosso sistema.
    public function __construct(ConexaoBD $conexaoBD, Seguranca $seguranca) {
        $this->conexaoBD = $conexaoBD;
        $this->seguranca = $seguranca;
    }

    // O método principal que orquestra a validação.
    public function validarUsuario(string $tipoUsuario, string $identificador, string $senha) {
        $tabela = ($tipoUsuario === 'medico') ? 'user_medicos' : 'user_pacientes';
        $coluna = ($tipoUsuario === 'medico') ? 'crm' : 'cpf';
        
        // Usamos nosso método seguro para a consulta.
        $sql = "SELECT * FROM $tabela WHERE $coluna = ?";
        $stmt = $this->conexaoBD->proteger_sql($sql, [$identificador]);
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Sucesso! Inicia a sessão completa.
            $this->iniciarSessaoCompleta($usuario, $tipoUsuario);
        } else {
            // Falha no login.
            $this->setErroLogin('Identificador ou senha incorretos.');
        }
    }

    // 2. O CORAÇÃO DA SOLUÇÃO
    // Este método foi reescrito para criar a sessão com todas as chaves de segurança.
    private function iniciarSessaoCompleta(array $usuario, string $tipoUsuario) {
        // Regenera o ID da sessão para máxima segurança contra session fixation.
        session_regenerate_id(true);

        // Define a chave principal que estava faltando.
        $_SESSION['usuario_id'] = $usuario['id'];
        
        // Define as outras chaves que a classe Seguranca vai verificar.
        $_SESSION['tipo_usuario'] = $tipoUsuario;
        $_SESSION['user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['last_activity'] = time();
        
        // (Opcional) Guarda dados extras que podem ser úteis.
        $_SESSION['usuario_nome'] = $usuario['nome'] ?? '';
        if ($tipoUsuario === 'medico') {
            $_SESSION['crm'] = $usuario['crm'];
        }

        // Força a escrita da sessão antes de redirecionar.
        session_write_close();

        // Redireciona para o dashboard correto.
        $dashboard = ($tipoUsuario === 'medico') ? 'dashboard_medico.php' : 'dashBoard_Paciente.php';
        header("Location: " . $dashboard);
        exit();
    }

    private function setErroLogin($mensagem) {
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: index.php'); // Envia o erro de volta para a index, onde está o login.
        exit();
    }
}

// 3. EXECUÇÃO
// Pega os dados sanitizados.
$tipo_usuario = $seguranca->sanitizar_entrada($_POST['tipo_usuario']);
$identificador = $seguranca->sanitizar_entrada($_POST['identificador']);
$senha = $_POST['senha']; // A senha não é sanitizada para não corromper o hash.

// Instancia e executa a validação.
$login = new ValidaLogin($conexaoBD, $seguranca);
$login->validarUsuario($tipo_usuario, $identificador, $senha);