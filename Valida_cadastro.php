<?php
// 1. INICIALIZAÇÃO CENTRALIZADA
// Substitui session_start() e a inclusão manual do banco.
require_once 'iniciar.php';

// 2. VALIDAÇÃO DE CSRF E MÉTODO
// Verificamos o método da requisição e o token de segurança primeiro.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Define a notificação e redireciona.
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido.'];
    header('Location: cadastrar.php');
    exit();
}
if (!$seguranca->validar_csrf_token($_POST['csrf_token'])) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Erro de validação de segurança. Tente enviar o formulário novamente.'];
    header('Location: cadastrar.php');
    exit();
}


class ValidaCadastro {
    private $conexaoBD;
    private $seguranca;
    private $dados;

    // O construtor agora recebe nossos objetos principais.
    public function __construct(ConexaoBD $conexaoBD, Seguranca $seguranca) {
        $this->conexaoBD = $conexaoBD;
        $this->seguranca = $seguranca;
    }

    // Valida e processa o cadastro. Retorna true em sucesso, ou redireciona em caso de erro.
    public function processarCadastro(array $postData) {
        // Sanitiza todos os dados de uma vez.
        $this->dados = $this->seguranca->sanitizar_entrada($postData);

        // Realiza as validações.
        if ($this->camposVazios()) {
            $this->setErro('Preencha todos os campos obrigatórios!');
        }
        if ($this->dados['senha'] !== $this->dados['confirmar_senha']) {
            $this->setErro('As senhas não coincidem!');
        }
        if ($this->usuarioJaExiste()) {
            $this->setErro('O CPF/CRM ou e-mail informado já está cadastrado!');
        }

        // Se passou por todas as validações, cadastra o usuário.
        return $this->cadastrarUsuario();
    }

    private function camposVazios() {
        $camposObrigatorios = ['email', 'senha', 'confirmar_senha', 'tipo_usuario', 'identificador'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($this->dados[$campo])) {
                return true;
            }
        }
        return false;
    }

    private function usuarioJaExiste() {
        $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
        $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';

        $sql = "SELECT id FROM $tabela WHERE $coluna = ? OR email = ? LIMIT 1";
        $params = [$this->dados['identificador'], $this->dados['email']];
        
        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        $resultado = $stmt->get_result();
        
        return $resultado->num_rows > 0;
    }

    private function cadastrarUsuario() {
        $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
        $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';
        
        $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO $tabela (email, senha, $coluna) VALUES (?, ?, ?)";
        $params = [$this->dados['email'], $senhaHash, $this->dados['identificador']];

        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        
        // Verifica se a inserção foi bem-sucedida.
        return $stmt->affected_rows > 0;
    }
    
    // Este método agora apenas define a mensagem e redireciona.
    private function setErro($mensagem) {
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: cadastrar.php');
        exit();
    }
}

// 3. CÓDIGO DE EXECUÇÃO
// Instanciamos a classe com os objetos já prontos do iniciar.php
$cadastro = new ValidaCadastro($conexaoBD, $seguranca);

// Processa o cadastro.
if ($cadastro->processarCadastro($_POST)) {
    // Se chegou aqui, o cadastro foi um sucesso.
    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Cadastro realizado com sucesso! Faça seu login.'];
    header('Location: index.php'); // Redireciona para o login na index.
    exit();
} else {
    // Se a função retornar false (o que não deve acontecer por causa do setErro), trata aqui.
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Ocorreu um erro inesperado durante o cadastro.'];
    header('Location: cadastrar.php');
    exit();
}
?>