<?php
// 1. FUNDAÇÃO E DEPENDÊNCIAS
require_once 'iniciar.php';
require_once 'CfmApiClient.php';

// Validações de segurança primárias
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: cadastrar.php');
    exit;
}

class ValidaCadastro {
    private $conexaoBD;
    private $seguranca;
    private $dados;

    public function __construct(ConexaoBD $conexaoBD, Seguranca $seguranca) {
        $this->conexaoBD = $conexaoBD;
        $this->seguranca = $seguranca;
    }

    public function processarCadastro(array $postData) {
        $this->dados = $this->seguranca->sanitizar_entrada($postData);

        // Validações básicas
        if (empty($this->dados['name']) || empty($this->dados['identificador']) || empty($this->dados['email']) || empty($this->dados['senha'])) {
             $this->setErro('Por favor, preencha todos os campos obrigatórios.');
        }
        if ($this->dados['senha'] !== $this->dados['confirmar_senha']) {
            $this->setErro('As senhas não coincidem!');
        }
        if ($this->usuarioJaExiste()) {
            $this->setErro('O CPF/CRM ou e-mail informado já está registado!');
        }

        // Fluxo de decisão: Médico ou Paciente?
        if ($this->dados['tipo_usuario'] === 'medico') {
            $this->processarCadastroMedico();
        } else {
            if ($this->cadastrarPaciente()) {
                $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Registo realizado com sucesso! Faça o seu login.'];
                header('Location: index.php');
                exit();
            }
        }
        
        // Se chegou aqui, algo deu errado
        $this->setErro('Ocorreu um erro inesperado durante o registo.');
    }

    private function processarCadastroMedico() {
        // Validação da API do CFM
        $apiCfm = new CfmApiClient();
        $medicosCfm = $apiCfm->buscarMedicos([
            'crmMedico' => $this->dados['identificador'],
            'ufMedico' => $this->dados['uf']
        ]);

        if (empty($medicosCfm['dados'])) {
            $this->setErro('O CRM/UF informado não foi encontrado na base de dados do CFM.');
        }

        $medicoApi = $medicosCfm['dados'][0];
        $situacaoApi = strtoupper($medicoApi['SITUACAO']);
        if ($situacaoApi !== 'ATIVO') {
            $this->setErro("O registo para este CRM encontra-se na situação '{$medicoApi['SITUACAO']}'. Apenas médicos com situação ATIVA podem se registar.");
        }
        
        $nomeOficial = $medicoApi['NO_MEDICO'];

        // Se a validação da API passou, regista o médico
        if ($this->cadastrarMedico($nomeOficial)) {
             $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Registo realizado com sucesso! Faça o seu login.'];
             header('Location: index.php');
             exit();
        } else {
             $this->setErro('Não foi possível concluir o seu registo de médico.');
        }
    }

    private function usuarioJaExiste(): bool {
        $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
        $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';
        $sql = "SELECT id FROM $tabela WHERE $coluna = ? OR email = ? LIMIT 1";
        $params = [$this->dados['identificador'], $this->dados['email']];
        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Regista um PACIENTE com todos os novos campos.
     */
    private function cadastrarPaciente(): bool {
        $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
        
        // Constrói o endereço completo
        $endereco = $this->dados['rua'] . ', ' . $this->dados['numero'];
        if (!empty($this->dados['complemento'])) $endereco .= ' - ' . $this->dados['complemento'];

        $sql = "INSERT INTO user_pacientes 
                    (nome_paciente, cpf, email, senha, data_nascimento, genero, nome_mae, convenio, profissao, telefone, endereco) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $this->dados['name'],
            $this->dados['identificador'],
            $this->dados['email'],
            $senhaHash,
            $this->dados['nascimento'],
            $this->dados['genero'],
            $this->dados['nome_mae'],
            $this->dados['convenio'],
            $this->dados['profissão'],
            $this->dados['telefone'],
            $endereco
        ];

        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        return $stmt->affected_rows > 0;
    }

    /**
     * Regista um MÉDICO.
     */
    private function cadastrarMedico(string $nomeOficial): bool {
        $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
        $endereco = $this->dados['rua'] . ', ' . $this->dados['numero'];
        if (!empty($this->dados['complemento'])) $endereco .= ' - ' . $this->dados['complemento'];

        // Adapte os campos conforme a sua tabela `user_medicos`
        $sql = "INSERT INTO user_medicos (nome, email, senha, crm, uf, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $nomeOficial,
            $this->dados['email'],
            $senhaHash,
            $this->dados['identificador'],
            $this->dados['uf'],
            $this->dados['telefone'],
            $endereco
        ];
        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        return $stmt->affected_rows > 0;
    }
    
    private function setErro($mensagem) {
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: cadastrar.php');
        exit();
    }
}

// EXECUÇÃO
$cadastro = new ValidaCadastro($conexaoBD, $seguranca);
$cadastro->processarCadastro($_POST);

