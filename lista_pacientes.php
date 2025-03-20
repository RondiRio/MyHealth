<!DOCTYPE html>
<?php
    include('bancoDeDados/sql/conexaoBD.php');
    session_start();
    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();

class Medico {
    private $conn;
    private $crm;

    public function __construct($conn, $crm) {
        $this->conn = $conn;
        $this->crm = $crm;
    }

    public function buscarDados() {
        $sql = "SELECT nome, email, telefone, crm, endereco, cidade, estado, data_nascimento, genero, ano_formacao, status_atual, foto_perfil FROM user_medicos WHERE crm = ?";
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

    public function listarPacientes() {
        $sql = "SELECT nome_paciente, cpf_paciente, ultima_consulta, alergias, tipo_sanguineo, cirurgias, exames, medicamentos_controlados, medicamentos_nao_controlados FROM consultas_realizadas WHERE medico_id = (SELECT id FROM user_medicos WHERE crm = ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $this->crm);
        $stmt->execute();
        $result = $stmt->get_result();

        $pacientes = [];
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
        return $pacientes;
    }
}

$crm = $_SESSION['crm'];
$medico = new Medico($conn, $crm);
$dadosMedico = $medico->buscarDados();
$pacientes = $medico->listarPacientes();
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
    <div class="sidebar">
        <h3 class="text-center">Médico</h3>
        <img src="images/image perfil.jpg" class="profile-img mx-auto d-block" alt="Foto de perfil">
        <p class="text-center">Dr. <?php echo htmlspecialchars($dadosMedico['nome']); ?></p>
        <a href="dashboard_medico.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="lista_pacientes.php"><i class="fas fa-user-md"></i> Pacientes</a>
        <a href="prontuarios.php"><i class="fas fa-notes-medical"></i> Prontuários</a>
        <a href="configuracao_medico.php"><i class="fas fa-cog"></i> Configurações</a>
        <button><a href="routes/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></button>
    </div>

    <div class="content">
        <h2>Dashboard do Médico</h2>
        <div class="card p-3">
            <h4>Seus Dados</h4>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($dadosMedico['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($dadosMedico['telefone']); ?></p>
            <p><strong>CRM:</strong> <?php echo htmlspecialchars($dadosMedico['crm']); ?></p>
        </div>

        <h3 id="pacientes">Pacientes Atendidos</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Última Consulta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($paciente['nome_paciente']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['cpf_paciente']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['ultima_consulta']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Adicionar Nova Consulta</h3>
        <form action="processa_consulta.php" method="POST">
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF do Paciente</label>
                <input type="text" class="form-control" id="cpf" name="cpf" required>
            </div>
            <div class="mb-3">
                <label for="data" class="form-label">Data da Consulta</label>
                <input type="date" class="form-control" id="data" name="data" required>
            </div>
            <div class="mb-3">
                <label for="motivo_consulta" class="form-label">Motivo da consulta</label>
                <input type="text" class="form-control" id="motivo_consulta" name="motivo_consulta" required>
            </div>
            <div class="mb-3">
                <label for="laudo" class="form-label">Laudo Clinico</label>
                <input type="text" class="form-control" id="laudo" name="laudo" required>
            </div>
            <div class="mb-3">
                <label for="medicamento" class="form-label">Medicamento Receitado</label>
                <input type="text" class="form-control" id="medicamento" name="medicamento" required>
            </div>
            <div class="mb-3">
                <label for="alergia" class="form-label">Alergias</label>
                <input type="text" class="form-control" id="alergia" name="alergia" required>
            </div>
            <div class="mb-3">
                <label for="tipo_sangue" class="form-label">Tipo sanguineo</label>
                <input type="text" class="form-control" id="tipo_sangue" name="tipo_sangue" required>
            </div>
            <div class="mb-3">
                <label for="cirurgia" class="form-label">Cirurgia</label>
                <input type="text" class="form-control" id="cirurgia" name="cirurgia" required>
            </div>
            <div class="mb-3">
                <label for="exames" class="form-label">Exames Feitos</label>
                <input type="text" class="form-control" id="exames" name="exames" required>
            </div>
            <div class="mb-3">
                <label for="medicamentos_control" class="form-label">Medicamentos Controlados</label>
                <input type="text" class="form-control" id="medicamentos_control" name="medicamentos_control" required>
            </div>
            <div class="mb-3">
                <label for="medicamentos_n_control" class="form-label">Medicamentos não Controlados</label>
                <input type="text" class="form-control" id="medicamentos_n_control" name="medicamentos_n_control" required>
            </div>
            <div class="mb-3">
                <label for="diagnostico" class="form-label">Diagnostico</label>
                <input type="text" class="form-control" id="diagnostico" name="diagnostico" required>
            </div>
            <div class="mb-3">
                <label for="receita" class="form-label">Receita</label>
                <input type="file" class="form-control" id="receita" name="receita" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Consulta</button>
        </form>
    </div>
</body>
</html>
