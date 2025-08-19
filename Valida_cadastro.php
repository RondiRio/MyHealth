<?php

// 1. INICIALIZAÇÃO CENTRALIZADA
require_once 'iniciar.php';

// 2. VALIDAÇÃO DE CSRF E MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

    public function __construct(ConexaoBD $conexaoBD, Seguranca $seguranca) {
        $this->conexaoBD = $conexaoBD;
        $this->seguranca = $seguranca;
    }

    public function processarCadastro(array $postData): bool {
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

        // --- Validação da API do CFM aqui ---
        if ($this->dados['tipo_usuario'] === 'medico') {
            // A UF agora vem de um campo separado
            $crm = $this->dados['identificador'];
            $uf = $this->dados['uf']; // Supondo que o nome do campo seja 'uf'
            
            $medicoInfo = $this->consultarCrmCfm($crm, $uf);
            
            if ($medicoInfo === null) {
        $this->setErro("A combinação de CRM ({$crm}) e UF ({$uf}) não foi encontrada. Por favor, verifique os dados e tente novamente.");
        } else {
            // A API encontrou o registro, você pode prosseguir
            $this->dados['nome_medico'] = $medicoInfo['NM_MEDICO'] ?? 'Nome não encontrado';
            $this->dados['estado_medico'] = $medicoInfo['SG_UF'] ?? 'UF não encontrada';
        }
            // Adiciona o nome e o estado do médico aos dados para salvar.
            $this->dados['nome_medico'] = $medicoInfo['NM_MEDICO'] ?? 'Nome não encontrado';
            $this->dados['estado_medico'] = $medicoInfo['SG_UF'] ?? 'UF não encontrada';
        }
        // ----------------------------------------------
        
        return $this->cadastrarUsuario();
    }

    private function camposVazios(): bool {
        $camposObrigatorios = ['email', 'senha', 'confirmar_senha', 'tipo_usuario', 'identificador'];
        
        // Se for médico, adiciona 'uf' à lista de campos obrigatórios
        if (isset($this->dados['tipo_usuario']) && $this->dados['tipo_usuario'] === 'medico') {
            $camposObrigatorios[] = 'uf';
        }

        foreach ($camposObrigatorios as $campo) {
            if (empty($this->dados[$campo])) {
                return true;
            }
        }
        return false;
    }

    private function usuarioJaExiste(): bool {
        $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
        $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';

        $sql = "SELECT id FROM $tabela WHERE $coluna = ? OR email = ? LIMIT 1";
        
        // Se for médico, o identificador é apenas o número do CRM
        $identificador = $this->dados['identificador'];
        
        $params = [$identificador, $this->dados['email']];
        
        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        $resultado = $stmt->get_result();
        
        return $resultado->num_rows > 0;
    }

    private function cadastrarUsuario(): bool {
        $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
        $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';
        
        $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
        
        if ($this->dados['tipo_usuario'] === 'medico') {
            $sql = "INSERT INTO $tabela (email, senha, $coluna, nome, estado) VALUES (?, ?, ?, ?, ?)";
            $params = [$this->dados['email'], $senhaHash, $this->dados['identificador'], $this->dados['nome_medico'], $this->dados['estado_medico']];
        } else {
            $sql = "INSERT INTO $tabela (email, senha, $coluna) VALUES (?, ?, ?)";
            $params = [$this->dados['email'], $senhaHash, $this->dados['identificador']];
        }

        $stmt = $this->conexaoBD->proteger_sql($sql, $params);
        
        return $stmt->affected_rows > 0;
    }
    
    private function consultarCrmCfm(string $crm, string $uf): ?array
{
    $url = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/buscar_medicos';
    
    // A API do CFM espera que você passe o CRM e UF dentro de um objeto 'medico'.
    // A estrutura deve ser mais direta, sem o array extra no nível superior.
    $data = [
        [
            'medico' => [
                'nome' => '',
                'ufMedico' => $uf,
                'crmMedico' => $crm,
                'municipioMedico' => '',
                'tipoInscricaoMedico' => '',
                'situacaoMedico' => '',
                'detalheSituacaoMedico' => '',
                'especialidadeMedico' => '',
                'areaAtuacaoMedico' => '',
            ],
            'page' => 1,
            'pageNumber' => 1,
            'pageSize' => 10,
        ]
    ];
    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $dado_pesquisa = $data[0]['medico']['ufMedico'];
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$data])); // <--- Note a adição do array aqui. Isso simula o comportamento da API.

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            return null;
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($result['dados'][0])) {
            return null;
        }
        
        // A API retorna um array de resultados, então pegamos o primeiro (e único)
        return $result['dados'][0];

        } catch (Exception $e) {
            return null;
        }
    }
    
    private function setErro(string $mensagem) {
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: cadastrar.php');
        exit();
    }
}

// 3. CÓDIGO DE EXECUÇÃO
$cadastro = new ValidaCadastro($conexaoBD, $seguranca);

if ($cadastro->processarCadastro($_POST)) {
    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Cadastro realizado com sucesso! Faça seu login.'];
    header('Location: index.php');
    exit();
} else {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Ocorreu um erro inesperado durante o cadastro.'];
    header('Location: cadastrar.php');
    exit();
}