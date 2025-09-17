<?php

require_once 'iniciar.php';

// Validações de segurança primárias
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$seguranca->validar_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => 'Acesso inválido ou formulário expirado.'];
    header('Location: ../publics/cadastrar.php');
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
        try {
            $this->dados = $this->seguranca->sanitizar_entrada($postData);
            $this->logDebug("Dados sanitizados recebidos", $this->dados);

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
                $this->logDebug("Processando cadastro de médico");
                $this->processarCadastroMedico();
            } else {
                $this->logDebug("Processando cadastro de paciente");
                if ($this->cadastrarPaciente()) {
                    $_SESSION['notificacao'] = ['tipo' => 'sucesso', 'mensagem' => 'Registo realizado com sucesso! Faça o seu login.'];
                    header('Location: ../publics/login.php');
                    exit();
                }
            }
            
            $this->setErro('Ocorreu um erro inesperado durante o registo.');
            
        } catch (Exception $e) {
            $this->logDebug("Erro em processarCadastro: " . $e->getMessage());
            throw $e; // Re-lança para o catch principal
        }
    }

    /**
     * Busca médico na API do CFM
     * @param string $crm
     * @param string $uf
     * @return array|null
     */
    private function buscarMedicoCFM(string $crm, string $uf): ?array {
        $url = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/buscar_medicos';
        
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
            $this->logDebug("Fazendo requisição para API CFM", ['url' => $url, 'data' => $data]);
            
            $ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $curlError = curl_error($ch);
                curl_close($ch);
                throw new Exception('cURL error: ' . $curlError);
            }
            
            curl_close($ch);
            
            $this->logDebug("Resposta da API CFM", [
                'http_code' => $httpCode,
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 500)
            ]);
            
            // Verificar se a resposta HTTP é válida
            if ($httpCode !== 200) {
                throw new Exception("API CFM retornou erro HTTP: $httpCode");
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->logDebug('Erro na API CFM: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca foto do médico na API do CFM
     * @param string $securityHash
     * @param string $crm
     * @param string $uf
     * @return array|null
     */
    private function buscarFotoMedicoCFM(string $securityHash, string $crm, string $uf): ?array {
        $url = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/buscar_foto';
        
        $data = [
            [
                'securityHash' => $securityHash,
                'crm' => $crm,
                'uf' => $uf,
            ]
        ];

        try {
            $ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $curlError = curl_error($ch);
                curl_close($ch);
                throw new Exception('cURL error: ' . $curlError);
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new Exception("API CFM Foto retornou erro HTTP: $httpCode");
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->logDebug('Erro na API CFM Foto: ' . $e->getMessage());
            return null;
        }
    }

    private function processarCadastroMedico() {
        try {
            $crm = $this->dados['identificador'];
            $uf = $this->dados['uf'];
            
            $this->logDebug("Processando cadastro médico", ['crm' => $crm, 'uf' => $uf]);

            // Validação específica para campos de médico
            if (empty($uf)) {
                $this->setErro('Por favor, selecione o estado (UF) onde o CRM foi registado.');
            }

            // Buscar médico na API do CFM
            $medicosCfm = $this->buscarMedicoCFM($crm, $uf);

            // Validação da resposta da API
            if ($medicosCfm === null) {
                $this->setErro('Não foi possível comunicar com o sistema do CFM. Tente novamente mais tarde.');
            }

            if (empty($medicosCfm['dados']) || !is_array($medicosCfm['dados'])) {
                $this->logDebug("API CFM retornou dados vazios", $medicosCfm);
                $this->setErro('O CRM/UF informado não foi encontrado na base de dados do CFM. Verifique os dados e tente novamente.');
            }

            $medicoApi = $medicosCfm['dados'][0];
            $this->logDebug("Dados do médico encontrado", $medicoApi);
            
            // Verificar se existem os campos essenciais
            if (!isset($medicoApi['SITUACAO'])) {
                $this->logDebug("Campo SITUACAO não encontrado", $medicoApi);
                $this->setErro('Dados incompletos retornados pelo CFM. Campo situação ausente.');
            }

            if (!isset($medicoApi['NM_MEDICO']) || empty(trim($medicoApi['NM_MEDICO']))) {
                $this->logDebug("Campo NM_MEDICO não encontrado ou vazio", $medicoApi);
                $this->setErro('Nome do médico não encontrado no CFM. Dados retornados: ' . json_encode($medicoApi));
            }

            $situacaoApi = strtoupper(trim($medicoApi['SITUACAO']));
            $nomeOficial = trim($medicoApi['NM_MEDICO']);
            
            $this->logDebug("Validação situação e nome", [
                'situacao_original' => $medicoApi['SITUACAO'],
                'situacao_processada' => $situacaoApi,
                'nome_oficial' => $nomeOficial
            ]);

            // Validar situação do médico - aceitar REGULAR e ATIVO
            if (!in_array($situacaoApi, ['REGULAR', 'ATIVO'])) {
                $situacao = $medicoApi['SITUACAO'];
                $this->setErro("O registo para este CRM encontra-se na situação '{$situacao}'. Apenas médicos com situação REGULAR ou ATIVA podem se registar.");
            }

            // Opcionalmente, buscar foto do médico (se disponível)
            $fotoMedico = null;
            if (isset($medicoApi['SECURITYHASH'], $medicoApi['NU_CRM'], $medicoApi['SG_UF'])) {
                $this->logDebug("Tentando buscar foto do médico");
                $fotoMedico = $this->buscarFotoMedicoCFM(
                    $medicoApi['SECURITYHASH'], 
                    $medicoApi['NU_CRM'], 
                    $medicoApi['SG_UF']
                );
            }

            // Se chegou até aqui, todas as validações passaram
            $this->logDebug("Tentando cadastrar médico", [
                'nome' => $nomeOficial,
                'crm' => $crm,
                'uf' => $uf
            ]);
            
            if ($this->cadastrarMedico($nomeOficial, $medicoApi, $fotoMedico)) {
                $_SESSION['notificacao'] = [
                    'tipo' => 'sucesso', 
                    'mensagem' => "Registo realizado com sucesso, Dr(a). {$nomeOficial}! Faça o seu login."
                ];
                header('Location: ../publics/login.php');
                exit();
            } else {
                $this->setErro('Não foi possível concluir o seu registo de médico. Erro na inserção no banco de dados.');
            }
            
        } catch (Exception $e) {
            $this->logDebug("Erro em processarCadastroMedico: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function usuarioJaExiste(): bool {
        try {
            $tabela = $this->dados['tipo_usuario'] === 'medico' ? 'user_medicos' : 'user_pacientes';
            $coluna = $this->dados['tipo_usuario'] === 'medico' ? 'crm' : 'cpf';
            $sql = "SELECT id FROM $tabela WHERE $coluna = ? OR email = ? LIMIT 1";
            $params = [$this->dados['identificador'], $this->dados['email']];
            
            $this->logDebug("Verificando se usuário já existe", [
                'tabela' => $tabela,
                'coluna' => $coluna,
                'identificador' => $this->dados['identificador'],
                'email' => $this->dados['email']
            ]);
            
            $stmt = $this->conexaoBD->proteger_sql($sql, $params);
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            
            $this->logDebug("Resultado verificação usuário", ['exists' => $exists]);
            return $exists;
            
        } catch (Exception $e) {
            $this->logDebug("Erro em usuarioJaExiste: " . $e->getMessage());
            throw $e;
        }
    }

    private function cadastrarPaciente(): bool {
        try {
            $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
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

            $this->logDebug("Cadastrando paciente", $params);
            $stmt = $this->conexaoBD->proteger_sql($sql, $params);
            $success = $stmt->affected_rows > 0;
            
            $this->logDebug("Resultado cadastro paciente", ['success' => $success]);
            return $success;
            
        } catch (Exception $e) {
            $this->logDebug("Erro em cadastrarPaciente: " . $e->getMessage());
            throw $e;
        }
    }

    private function cadastrarMedico(string $nomeOficial, array $dadosCfm, ?array $fotoCfm = null): bool {
        try {
            $senhaHash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
            $endereco = $this->dados['rua'] . ', ' . $this->dados['numero'];
            if (!empty($this->dados['complemento'])) $endereco .= ' - ' . $this->dados['complemento'];

            // Extrair dados adicionais do CFM se disponíveis
            $especialidade = $dadosCfm['ESPECIALIDADE'] ?? '';
            $situacao = $dadosCfm['SITUACAO'] ?? 'REGULAR';
            $foto_base64 = null;
            
            if ($fotoCfm && isset($fotoCfm['dados']['foto'])) {
                $foto_base64 = $fotoCfm['dados']['foto'];
            }

            // Verificar se as colunas extras existem na tabela antes de tentar inserir
            $sql = "INSERT INTO user_medicos (nome, email, senha, crm, uf, telefone, endereco) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $nomeOficial,
                $this->dados['email'],
                $senhaHash,
                $this->dados['identificador'],
                $this->dados['uf'],
                $this->dados['telefone'],
                $endereco
            ];
            
            // Tentar inserir com campos extras se existirem
            try {
                $sqlExtended = "INSERT INTO user_medicos (
                        nome, email, senha, crm, uf, telefone, endereco, 
                        especialidade, situacao_cfm, foto_cfm, dados_cfm_json, 
                        data_cadastro, verificado_cfm
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)";
                
                $paramsExtended = [
                    $nomeOficial,
                    $this->dados['email'],
                    $senhaHash,
                    $this->dados['identificador'],
                    $this->dados['uf'],
                    $this->dados['telefone'],
                    $endereco,
                    $especialidade,
                    $situacao,
                    $foto_base64,
                    json_encode($dadosCfm)
                ];
                
                $this->logDebug("Tentando inserir com campos estendidos", $paramsExtended);
                $stmt = $this->conexaoBD->proteger_sql($sqlExtended, $paramsExtended);
                
            } catch (Exception $e) {
                // Se der erro, usar inserção básica
                $this->logDebug("Erro com campos estendidos, usando inserção básica: " . $e->getMessage());
                $stmt = $this->conexaoBD->proteger_sql($sql, $params);
            }
            
            $success = $stmt->affected_rows > 0;
            $this->logDebug("Resultado cadastro médico", ['success' => $success]);
            return $success;
            
        } catch (Exception $e) {
            $this->logDebug("Erro em cadastrarMedico: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function setErro($mensagem) {
        $this->logDebug("Erro definido: " . $mensagem);
        $_SESSION['notificacao'] = ['tipo' => 'erro', 'mensagem' => $mensagem];
        header('Location: ../publics/cadastrar.php');
        exit();
    }
    
    private function logDebug($message, $data = null) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [ValidaCadastro] $message";
        if ($data !== null) {
            $logMessage .= " | Data: " . print_r($data, true);
        }
        error_log($logMessage);
    }
}

// EXECUÇÃO
try {
    error_log("[" . date('Y-m-d H:i:s') . "] Iniciando processamento de cadastro");
    error_log("[" . date('Y-m-d H:i:s') . "] POST recebido: " . print_r($_POST, true));
    
    $cadastro = new ValidaCadastro($conexaoBD, $seguranca);
    $cadastro->processarCadastro($_POST);
    
} catch (Exception $e) {
    $errorMessage = 'Erro no processamento de cadastro: ' . $e->getMessage();
    $errorFile = 'Arquivo: ' . $e->getFile() . ' | Linha: ' . $e->getLine();
    $errorTrace = 'Stack trace: ' . $e->getTraceAsString();
    
    error_log($errorMessage);
    error_log($errorFile);
    error_log($errorTrace);
    echo '<pre>';
    print_r($_SESSION['notificacao'] = [
        'tipo' => 'erro', 
        'mensagem' => 'Ocorreu um erro inesperado. Tente novamente. (Médico não registrado)',
        'fileError' => $errorFile,
        // 'errorTrace' => $errorTrace
    ]);
    echo '</pre>';

     echo '<pre>';
    var_dump($e);

    echo '</pre>';
    header('Location: ../publics/cadastrar.php');
    exit;
} catch (Error $e) {
    $errorMessage = 'Erro fatal no processamento de cadastro: ' . $e->getMessage();
    $errorFile = 'Arquivo: ' . $e->getFile() . ' | Linha: ' . $e->getLine();
    
    error_log($errorMessage);
    error_log($errorFile);
    
    $_SESSION['notificacao'] = [
        'tipo' => 'erro', 
        'mensagem' => 'Erro crítico no sistema. Contate o administrador.'
    ];
    header('Location: ../publics/cadastrar.php');
    exit;
}

?>