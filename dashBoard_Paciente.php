<!DOCTYPE html>
<?php 
    include("bancoDeDados/sql/conexaoBD.php");

    session_start();
    
    if (!isset($_SESSION['cpf']) || $_SESSION['tipo_usuario'] !== 'paciente') {
        session_destroy();
        header("Location: index.php");
        exit();
    }
    
    $conexao = new ConexaoBD();
    $conn = $conexao->getConexao();
    
    class Paciente {
        private $conn;
        private $cpf;
    
        public function __construct($conn, $cpf) {
            $this->conn = $conn;
            $this->cpf = $cpf;
        }
    
        public function buscarDadosPaciente() {
            $query = 'SELECT nome, email, telefone, data_nascimento, sexo, tipo_sanguineo, foto_perfil FROM user_pacientes WHERE cpf = ?';
            $stmt = $this->conn->prepare($query);
    
            if (!$stmt) {
                die("Erro na preparação da consulta: " . $this->conn->error);
            }
    
            $stmt->bind_param('s', $this->cpf);
            $stmt->execute();
            $resultado = $stmt->get_result();
    
            if ($resultado->num_rows > 0) {
                return $resultado->fetch_assoc(); // Retorna os dados do paciente
            } else {
                return null; // Nenhum paciente encontrado
            }
        }
    }
    
    $paciente = new Paciente($conn, $_SESSION['cpf']);
    $dadosPaciente = $paciente->buscarDadosPaciente();
    
    if (!$dadosPaciente) {
        echo "Paciente não encontrado.";
        exit();
    }
    
    // Teste: Exibir os dados do paciente
    // print_r($dadosPaciente);

?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Paciente</title>
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
        <h3 class="text-center">Paciente</h3>
        <img src="<?php $dadosPaciente['foto_perfil']?>" class="profile-img mx-auto d-block" alt="Foto de perfil">
        <p class="text-center"><?php echo $dadosPaciente['nome']?></p>
        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#"><i class="fas fa-file-medical"></i> Prontuário</a>
        <a href="#"><i class="fas fa-hospital"></i> Hospitais Visitados</a>
        <a href="#"><i class="fas fa-cog"></i> Configurações</a>
        <button><a href="routes/logout.php"><i class="fas fa-exit">Sair</i></a></button>
        
    </div>

    <!-- Conteúdo Principal -->
    <div class="content">
        <h2>Dashboard do Paciente</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Alergias</h5>
                    <p>Nenhuma registrada</p>
                </div>
            </div>
            <br>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Tipo sanguineo</h5>
                    <p>O+</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Última Consulta</h5>
                    <p>12/02/2025 - Dr. Marcos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Grau de Visão</h5>
                    <p>OD: -1.5 | OE: -1.0</p>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Último Hospital Visitado</h5>
                    <p>Hospital São Lucas</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Último diagnóstico apresentado</h5>
                    <p>Dermatite Ceborreica</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Último medicamento administrado</h5>
                    <p>Escabin - aplicar de 12 em 12h </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Últimas Visitas ao Hospital</h5>
                    <ul>
                        <li>São Lucas - 10/01/2025</li>
                        <li>Santa Maria - 15/12/2024</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
