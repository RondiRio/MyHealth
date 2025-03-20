<!DOCTYPE html>
<?php
    include('bancoDeDados/sql/conexaoBD.php');
    session_start();
    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();
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
            return $result->fetch_assoc(); // Retorna os dados do médico
        } else {
            throw new Exception("Nenhum médico encontrado para o CRM <br> " . htmlspecialchars($this->crm));
        }
    }
}
// Instancia a classe Médico
$crm = $_SESSION['crm'];

$medico = new Medico($conn, $crm);
$dadosMedico = $medico->buscarDados();


// Consultar o total de consultas realizadas pelo médico
$sqlConsultas = "SELECT COUNT(id) AS total_consultas
                 FROM consultas_realizadas
                 WHERE medico_id = (SELECT id FROM user_medicos WHERE crm = ?)";
$stmtConsultas = $conn->prepare($sqlConsultas);
$stmtConsultas->bind_param("s", $crm);
$stmtConsultas->execute();
$resultConsultas = $stmtConsultas->get_result();
$totalConsultas = $resultConsultas->fetch_assoc()['total_consultas'];
// $idValor= 0;
// for($idValor < 0)


// Consultar o total de pacientes atendidos
$sqlPacientes = "SELECT COUNT(DISTINCT paciente_id) AS total_pacientes
                 FROM prontuarios
                 WHERE medico_id = (SELECT id FROM user_medicos WHERE crm = ?)";
                 
$stmtPacientes = $conn->prepare($sqlPacientes);
$stmtPacientes->bind_param("s", $crm);
$stmtPacientes->execute();
$resultPacientes = $stmtPacientes->get_result();
$totalPacientes = $resultPacientes->fetch_assoc()['total_pacientes'];


// Consultar as 5 consultas mais recentes
$sqlConsultasRecentes = "SELECT c.nome_paciente, c.cpf_paciente, c.ultima_consulta, c.diagnostico
                          FROM consultas_realizadas c
                          WHERE c.medico_id = (SELECT id FROM user_medicos WHERE crm = ?)
                          ORDER BY c.ultima_consulta DESC
                          LIMIT 5";

$stmtConsultasRecentes = $conn->prepare($sqlConsultasRecentes);
$stmtConsultasRecentes->bind_param("s", $crm);
$stmtConsultasRecentes->execute();
$resultConsultasRecentes = $stmtConsultasRecentes->get_result();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $diagnostico = $_POST['diagnostico'];

    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();

    $sql = "UPDATE consultas_realizadas SET nome_paciente = ?, cpf_paciente = ?, diagnostico = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $nome, $cpf, $diagnostico, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}


// Buscar hospitais vinculados ao médico
$sql = "SELECT h.nome FROM hospitais h 
        JOIN medicos_hospitais mh ON h.id = mh.hospital_id
        WHERE mh.medico_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body { display: flex; }
        .sidebar { width: 250px; background: #343a40; color: white; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { background: #495057; }
        .content { flex: 1; padding: 20px; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .section-header { margin-bottom: 20px; }
        .card { margin-top: 20px; }
        .card-header { font-weight: bold; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <!-- Menu Lateral -->
    <div class="sidebar">
        <h3 class="text-center">Médico</h3>
        <img src="images/<?php echo $dadosMedico['foto_perfil'] ?>" class="profile-img mx-auto d-block" alt="Foto de perfil">
        <p class="text-center">Dr. <?php echo $dadosMedico['nome'] ?></p> 
        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
        <a href="lista_pacientes.php"><i class="fas fa-user-md"></i> Pacientes</a>
        <a href="#"><i class="fas fa-notes-medical"></i> Prontuários</a>
        <a href="configura_hospital.php"><i class="fas fa-hospital"></i> Hospital</a>
        <a href="configuracao_medico.php"><i class="fas fa-cog"></i> Configurações</a>
        <button><a href="routes/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></button>
    </div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <h2 class="section-header">Dashboard do Médico</h2>

        <div class="card p-3">
            <h4>Seus Dados</h4>
            <p><strong>Email:</strong> <?php echo $dadosMedico['email'] ?></p>
            <p><strong>Telefone:</strong> <?php echo $dadosMedico['telefone'] ?></p>
            <p><strong>CRM:</strong> <?php echo $dadosMedico['crm'] ?></p>
            <p><strong>Endereço:</strong> <?php echo $dadosMedico['endereco'] ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo $dadosMedico['data_nascimento'] ?></p>
            <p><strong>Gênero:</strong> <?php echo $dadosMedico['genero'] ?></p>
            <p><strong>Ano de Formação:</strong> <?php echo $dadosMedico['ano_formacao'] ?></p>
            <p><strong>Status:</strong> <?php echo $dadosMedico['status_atual'] ?></p>
            <p><strong>Hospital atual:</strong> <?php while ($hospital = $result->fetch_assoc()) { ?>
        <li><?php echo $hospital['nome']; ?></li>
    <?php } ?></p>
        </div>
        
        <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Atendimentos de Hoje</h5>
                    <p id="hoje" class="card-text"><?php echo $totalConsultas; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Atendimentos da Semana</h5>
                    <p id="semana" class="card-text"><?php echo $totalPacientes; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Atendimentos do Mês</h5>
                    <p id="mes" class="card-text"><?php echo $totalConsultas; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total de Pacientes</h5>
                    <p id="totalPacientes" class="card-text"><?php echo $totalPacientes; ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4>Consultas Recentes</h4>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">CPF</th>
            <th scope="col">Última Consulta</th>
            <th scope="col">Diagnóstico</th>
            <th scope="col">Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php while($consulta = $resultConsultasRecentes->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $consulta['nome_paciente']; ?></td>
                <td><?php echo $consulta['cpf_paciente']; ?></td>
                <td><?php echo $consulta['ultima_consulta']; ?></td>
                <td><?php echo $consulta['diagnostico']; ?></td>
                <td><button class="btn btn-primary edit-btn" onclick="exibeModalEditar(event)" data-diagnostico="<?php echo $consulta['diagnostico']; ?>">Editar</button></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editarModalLabel">Editar Consulta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarConsulta" method="post" action="editar_consulta.php">

          <div class="mb-3">
            <label for="alergias" class="form-label">Alergias</label>
            <input type="text" class="form-control" id="alergias" >
          </div>
          <div class="mb-3">
            <label for="cirurgias" class="form-label">Cirurgias</label>
            <input type="text" class="form-control" id="cirurgias" >
          </div>
          <div class="mb-3">
            <label for="examesFeitos" class="form-label">Exames feitos</label>
            <textarea class="form-control" id="examesFeitos" ></textarea>
          </div>
          <div class="mb-3">
            <label for="meidcamentosControlados" class="form-label">Medicamentos controlados</label>
            <input type="text" class="form-control" id="meidcamentosControlados" >
          </div>
          <div class="mb-3">
            <label for="meidcamentosNaoControlados" class="form-label">Medicamentos não controlados</label>
            <input type="text" class="form-control" id="meidcamentosNaoControlados" >
          </div>
          <input type="hidden" id="consultaId">
          <button type="submit" class="btn btn-success">Salvar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </form>
      </div>
    </div>
  </div>
</div>


    <h4>Detalhamento do Paciente</h4>
    <form id="detalhesPaciente">
        <div class="mb-3">
            <label class="form-label">Nome:</label>
            <input type="text" class="form-control" id="nomePaciente" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Alergias:</label>
            <input type="text" class="form-control" id="alergiasPaciente" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Última Consulta:</label>
            <input type="date" class="form-control" id="ultimaConsulta" disabled>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>

    <h4>Evolução dos Atendimentos</h4>
    <canvas id="graficoAtendimentos" width="400" height="200"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('graficoAtendimentos').getContext('2d');
        var graficoAtendimentos = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Atendimentos Semanais',
                    data: [12, 19, 3, 5, 2, 3, 10],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        class ModalEditarConsulta{
            constructor(modalId, fromId){
                this.modal = new bootstrap.Modal(document.getElementById(modalId));
                this.form = document.getElementById(fromId);
                this.addEventListeners();

            }
            exibeModalEditar(event){
                const button = event.target;
                const dados ={
                    alergias: button.getAttribute('data-alergias'),
                    cirurgias: button.getAttribute("data-cirurgias"),
                    examesFeitos: button.getAttribute('data-examesFeitos'),
                    medicamentosControlados: button.getAttribute('data-medicamentosControlados'),
                    diagnostico: button.getAttribute('data-diagnostico')
                };
                this.preencherFormulario(dados);
                this.modal.show();
            }
            preencherFormulario(dados){
            document.getElementById('diagnostico').value = dados.diagnostico;
            document.getElementById('alergias').value = dados.alergias;
            document.getElementById('examesFeitos').value = dados.examesFeitos;
            document.getElementById('medicamentosControlados').value = dados.medicamentosControlados
            }
            enviarFormulario(event){
                event.preventDefault();
                const dados = new FormData(this.form);
                fetch('editar_consulta.php', {
                    method: 'POST',
                    body: dados
                })
                .then(response => response.json())
                .then(data =>{
                    if(data.success){
                        alert('consulta atualizada com sucesso!');
                        location.reload();
                    }
                    else{
                        alert('erro ao atualizar os dados');

                    }
                })
                .catch(error=> console.error("Erro ao enviar os dados: ", error));
            }

            addEventListeners(){
                document.querySelector('.edit-btn').forEach(button => {
                    button.addEventListeners('click', this.exibeModalEditar.bind(this));
                });

                this.form.addEventListener('submit', this.enviarFormulario(bind(this)))
            }
        
        }
        const ModalEditarConsulta = new ModalEditarConsulta('editarModal', 'formEditarConsulta');
    </script>
</body>
</html>
