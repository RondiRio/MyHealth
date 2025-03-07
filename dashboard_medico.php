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
        // var_dump($_SESSION);
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
// try {
//     $dadosMedico = $medico->buscarDados();
//     echo "<h2>Dados do Médico</h2>";
//     echo "<pre>";
//     print_r($dadosMedico); // Exibe os dados de forma legível
//     echo "</pre>";
// } catch (Exception $e) {
//     echo "Erro: " . $e->getMessage();
// }
?>


<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { display: flex; }
        .sidebar { width: 250px; background: #343a40; color: white; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { background: #495057; }
        .content { flex: 1; padding: 20px; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
    <!-- Menu Lateral -->
    <div class="sidebar">
        <h3 class="text-center">Médico</h3>
        <img src="images/image perfil.jpg" class="profile-img mx-auto d-block" alt="Foto de perfil">
        <p class="text-center">Dr. <?php print_r($medico->buscarDados()['nome'])?></p> 
        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#"><i class="fas fa-user-md"></i> Pacientes</a>
        <a href="#"><i class="fas fa-notes-medical"></i> Prontuários</a>
        <a href="configuracao_medico.php"><i class="fas fa-cog"></i> Configurações</a>
        <button><a href="routes/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></button>
    </div>
<!-- Conteúdo Principal -->
<div class="content">
    
    <h2>Dashboard do Médico</h2>
    <div class="card p-3">
        <h4>Seus Dados</h4>
        <p><strong>Email:</strong> <?php print_r($medico->buscarDados()['email'])?></p>
        <p><strong>Telefone:</strong> <?php print_r($medico->buscarDados()['telefone'])?></p>
        <p><strong>CRM:</strong> <?php print_r($medico->buscarDados()['crm']) ?></p>
        <p><strong>Endereço:</strong> <?php print_r($medico->buscarDados()['endereco'])?></p>
        <p><strong>Data de Nascimento:</strong> <?php print_r($medico->buscarDados()['data_nascimento'])?></p>
        <p><strong>Gênero:</strong> <?php print_r($medico->buscarDados()['genero'])?></p>
        <p><strong>Ano de Formação:</strong> <?php print_r($medico->buscarDados()['ano_formacao'])?></p>
        <p><strong>Status:</strong> <?php print_r($medico->buscarDados()['status_atual'])?></p>
    </div>
</div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <h2>Dashboard do Médico</h2>
        <form id="buscarPaciente" class="mb-4">
            <label for="cpf" class="form-label">Buscar Paciente por CPF:</label>
            <input type="text" id="cpf" class="form-control" placeholder="Digite o CPF">
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
        </form>

        <div id="dadosPaciente" class="d-none">
            <h3>Dados do Paciente</h3>
            <form>
                <div class="mb-3">
                    <label class="form-label">Nome:</label>
                    <input type="text" class="form-control" id="nomePaciente" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alergias:</label>
                    <input type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Última Consulta:</label>
                    <input type="date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Diagnóstico:</label>
                    <input type="text" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>

    <!-- Modal para novo paciente -->
    <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Paciente não encontrado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Deseja cadastrar um novo paciente?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('buscarPaciente').addEventListener('submit', function(event) {
            event.preventDefault();
            const cpf = document.getElementById('cpf').value;
            
            // Simulação de busca de paciente
            if (cpf === "123.456.789-00") {
                document.getElementById('dadosPaciente').classList.remove('d-none');
                document.getElementById('nomePaciente').value = "João da Silva";
            } else {
                var modal = new bootstrap.Modal(document.getElementById('modalCadastro'));
                modal.show();
            }
        });
    </script>
</body>
</html>
