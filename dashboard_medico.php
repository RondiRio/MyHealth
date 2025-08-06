<!DOCTYPE html>
<?php
    include('bancoDeDados/sql/conexaoBD.php');
    session_start();
    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();

    // echo'<pre>';
    // print_r($_SESSION);

    // echo'</pre>';
    // Classe responsável por manipular os dados do médico
    class Medico {
        private $conn;
        private $crm;

        public function __construct($conn, $crm) {
            $this->conn = $conn;
            $this->crm = $crm;
        }

        public function buscarDados() {
            
            $sql = "SELECT nome, email, telefone, crm, endereco, cidade, estado, data_nascimento, genero, ano_formacao, status_atual, foto_perfil 
                    FROM user_medicos 
                    WHERE crm = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Erro na preparação da consulta: " . $this->conn->error);
            }

            $stmt->bind_param("s", $this->crm);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                throw new Exception("Nenhum médico encontrado para o CRM <br> " . htmlspecialchars($this->crm));
            }
        }
    }

    $crm = $_SESSION['crm'];
    $medico = new Medico($conn, $crm);
    $dadosMedico = $medico->buscarDados();
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/DashMedico.css">
</head>
<body>
    <!-- Menu Lateral -->
    <div class="sidebar d-flex flex-column align-items-center">
        <h3 class="text-center w-100 mb-3">Painel Médico</h3>
        <img src="images/image perfil.jpg" class="profile-img" alt="Foto de perfil">
        <div class="profile-name text-center w-100 mb-4">
            Dr. <?php echo htmlspecialchars($dadosMedico['nome']); ?>
        </div>
        <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="consulta.php"><i class="fas fa-user-md"></i> Consulta</a>
        <a href="prontuario.php"><i class="fas fa-notes-medical"></i> Prontuários</a>
        <a href="configuracao_medico.php"><i class="fas fa-cog"></i> Configurações</a>
        <form action="routes/logout.php" method="post" class="w-100">
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</button>
        </form>
    </div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <h2 class="mb-4" style="font-weight:700;">Bem-vindo, Dr. <?php echo htmlspecialchars($dadosMedico['nome']); ?></h2>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card p-4 mb-4">
                    <h4 class="mb-3">Seus Dados</h4>
                    <div class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($dadosMedico['email']); ?></div>
                    <div class="mb-2"><strong>Telefone:</strong> <?php echo htmlspecialchars($dadosMedico['telefone']); ?></div>
                    <div class="mb-2"><strong>CRM:</strong> <?php echo htmlspecialchars($dadosMedico['crm']); ?></div>
                    <div class="mb-2"><strong>Endereço:</strong> <?php echo htmlspecialchars($dadosMedico['endereco']); ?></div>
                    <div class="mb-2"><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($dadosMedico['data_nascimento']); ?></div>
                    <div class="mb-2"><strong>Gênero:</strong> <?php echo htmlspecialchars($dadosMedico['genero']); ?></div>
                    <div class="mb-2"><strong>Ano de Formação:</strong> <?php echo htmlspecialchars($dadosMedico['ano_formacao']); ?></div>
                    <div class="mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($dadosMedico['status_atual']); ?></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-4 mb-4">
                    <h4 class="mb-3">Consultar Histórico do Paciente</h4>
                    <form id="buscarPaciente" class="mb-3" action="processa_buscar_consulta.php" method="POST">
                        <label for="cpf" class="form-label">Buscar Paciente por CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="form-control mb-2" placeholder="Digite o CPF" required>
                        <button type="submit" class="btn btn-primary w-100">Buscar Consultas</button>
                    </form>

                    <div id="resultadoBusca" class="d-none">
                        <div id="dadosPaciente">
                            <h5 class="mb-3">Dados do Paciente</h5>
                            <div class="mb-3 p-3 bg-light rounded">
                                <div><strong>Nome:</strong> <span id="nomePaciente"></span></div>
                                <div><strong>CPF:</strong> <span id="cpfPaciente"></span></div>
                                <div><strong>Email:</strong> <span id="emailPaciente"></span></div>
                                <div><strong>Telefone:</strong> <span id="telefonePaciente"></span></div>
                            </div>
                        </div>
                        
                        <div id="consultasPaciente">
                            <h5 class="mb-3">Histórico de Consultas</h5>
                            <div id="listaConsultas"></div>
                        </div>
                    </div>

                    <div id="pacienteNaoEncontrado" class="d-none">
                        <div class="alert alert-no-patient" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Paciente não encontrado!</strong> Este CPF não está registrado na base de dados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard de Atendimentos -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="dashboard-metric">
                    <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    <div>
                        <div class="metric-title">Atendimentos Hoje</div>
                        <div class="metric-value" id="atendimentos-dia">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-metric">
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="metric-title">Atendimentos no Mês</div>
                        <div class="metric-value" id="atendimentos-mes">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-metric">
                    <div class="icon"><i class="fas fa-calendar"></i></div>
                    <div>
                        <div class="metric-title">Atendimentos no Ano</div>
                        <div class="metric-value" id="atendimentos-ano">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulação de dados de atendimentos
        document.getElementById('atendimentos-dia').textContent = 5;
        document.getElementById('atendimentos-mes').textContent = 42;
        document.getElementById('atendimentos-ano').textContent = 320;

        // Função para buscar consultas do paciente
        document.getElementById('buscarPaciente').addEventListener('submit', function(e) {
            e.preventDefault();

            const cpf = document.getElementById('cpf').value.trim();
            
            // Ocultar resultados anteriores
            document.getElementById('resultadoBusca').classList.add('d-none');
            document.getElementById('pacienteNaoEncontrado').classList.add('d-none');

            // Mostrar loading
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Buscando...';
            submitBtn.disabled = true;

            // Fazer requisição AJAX para buscar o paciente e suas consultas
            fetch('processa_buscar_consulta.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({cpf: cpf})
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text(); // Primeiro pegar como texto para debug
            })
            .then(text => {
                console.log('Response text:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);

                    if (data.success) {
                        // Paciente encontrado - mostrar dados
                        document.getElementById('nomePaciente').textContent = data.paciente.nome;
                        document.getElementById('cpfPaciente').textContent = data.paciente.cpf;
                        document.getElementById('emailPaciente').textContent = data.paciente.email || 'Não informado';
                        document.getElementById('telefonePaciente').textContent = data.paciente.telefone || 'Não informado';

                        // Mostrar consultas
                        const listaConsultas = document.getElementById('listaConsultas');
                        if (data.consultas && data.consultas.length > 0) {
                            listaConsultas.innerHTML = '';
                            data.consultas.forEach(consulta => {
                                const consultaDiv = document.createElement('div');
                                consultaDiv.className = 'consulta-item';
                                consultaDiv.innerHTML = `
                                    <div class="consulta-header">Consulta - ${formatarData(consulta.data_consulta)}</div>
                                    <div class="consulta-info"><strong>ID Médico:</strong> ${consulta.id_medico}</div>
                                    <div class="consulta-info"><strong>CPF Paciente:</strong> ${consulta.cpf_paciente}</div>
                                    <div class="consulta-info"><strong>Nome Paciente:</strong> ${consulta.nome_paciente}</div>
                                    <div class="consulta-info"><strong>Especialidade:</strong> ${consulta.especialidade}</div>
                                    <div class="consulta-info"><strong>Status da Consulta:</strong> ${consulta.status_consulta}</div>
                                    <div class="consulta-info"><strong>Anamnese:</strong> ${consulta.anamnese}</div>
                                    <div class="consulta-info"><strong>Queixas e histórico:</strong> ${consulta.queixas_historico || ''}</div>
                                    <div class="consulta-info"><strong>Exame Físico:</strong> ${consulta.exame_fisico}</div>
                                    <div class="consulta-info"><strong>Observações do exame físico:</strong> ${consulta.observacoes_exame_fisico || ''}</div>
                                    <div class="consulta-info"><strong>Hipótese Diagnóstica:</strong> ${consulta.hipotese_diagnostica}</div>
                                    <div class="consulta-info"><strong>Diagnóstico Final:</strong> ${consulta.diagnostico_final}</div>
                                    <div class="consulta-info"><strong>Tratamento Proposto:</strong> ${consulta.tratamento_proposto}</div>
                                    <div class="consulta-info"><strong>Observações Privadas:</strong> ${consulta.observacoes_privadas}</div>
                                    <div class="consulta-info"><strong>Visível para paciente:</strong> ${consulta.visivel_para_paciente == 1 ? 'Sim' : 'Não'}</div>
                                    <div class="consulta-info"><strong>Criado em:</strong> ${formatarData(consulta.criado_em)}</div>
                                `;
                                listaConsultas.appendChild(consultaDiv);
                            });
                        } else {
                            listaConsultas.innerHTML = '<div class="alert alert-info">Nenhuma consulta encontrada para este paciente.</div>';
                        }

                        document.getElementById('resultadoBusca').classList.remove('d-none');
                    } else {
                        // Paciente não encontrado
                        document.getElementById('pacienteNaoEncontrado').classList.remove('d-none');
                    }
                } catch (parseError) {
                    console.error('Erro ao fazer parse do JSON:', parseError);
                    console.error('Texto recebido:', text);
                    alert('Erro: Resposta inválida do servidor. Verifique o console para mais detalhes.');
                }
            })
            .catch(error => {
                console.error('Erro completo:', error);
                console.error('Erro tipo:', typeof error);
                console.error('Erro stack:', error.stack);
                alert('Erro ao buscar dados do paciente: ' + error.message + '. Verifique o console para mais detalhes.');
            })
            .finally(() => {
                // Restaurar botão
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Função para formatar data
        function formatarData(dataString) {
            if (!dataString) return '';
            const data = new Date(dataString);
            if (isNaN(data.getTime())) return dataString;
            return data.toLocaleDateString('pt-BR');
        }
    </script>
</body>
</html>