<!DOCTYPE html>
<?php
    // include('bancoDeDados/sql/conexaoBD.php');
    include_once('../controller/iniciar.php');
    // session_start();
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
    <title>Dashboard Médico - MediCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --medical-blue: #2c5aa0;
            --medical-light-blue: #4a7bc8;
            --medical-teal: #17a2b8;
            --medical-green: #28a745;
            --medical-gray: #6c757d;
            --medical-purple: #6f42c1;
            --medical-orange: #fd7e14;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--medical-blue) 0%, var(--medical-light-blue) 100%);
            color: white;
            min-height: 100vh;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .logo i {
            font-size: 2rem;
            color: #fff;
        }

        .logo h3 {
            color: #fff;
            font-weight: 600;
            margin: 0;
        }

        .doctor-profile {
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.2);
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .profile-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .doctor-crm {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
        }

        .nav-section {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin-bottom: 0.25rem;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #fff;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-left-color: #fff;
        }

        .nav-link i {
            width: 20px;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .logout-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: #fff;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: calc(100% - 3rem);
            margin: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            color: #fff;
        }

        .content {
            flex: 1;
            padding: 1rem;
            padding-left: .9rem;
            overflow-y: auto;
        }

        .header-section {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--medical-purple), var(--medical-teal), var(--medical-blue));
        }

        .page-title {
            color: var(--medical-blue);
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-subtitle {
            color: var(--medical-gray);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .dashboard-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .metric-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
        }

        .metric-icon.today { background: linear-gradient(135deg, var(--medical-green), #20c997); }
        .metric-icon.month { background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue)); }
        .metric-icon.year { background: linear-gradient(135deg, var(--medical-purple), #8e6ec8); }

        .metric-info h3 {
            color: var(--medical-blue);
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
        }

        .metric-info p {
            color: var(--medical-gray);
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .main-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card, .search-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--medical-blue), var(--medical-light-blue));
            color: #fff;
            padding: 1.5rem 2rem;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--medical-blue);
        }

        .info-value {
            color: var(--medical-gray);
            font-weight: 500;
        }

        .search-form {
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--medical-blue);
            margin-bottom: 0.75rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 90, 160, 0.25);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--medical-teal), #17a2b8);
            border: none;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
            color: #fff;
        }

        .patient-result {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .patient-header {
            color: var(--medical-blue);
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .patient-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .patient-field {
            display: flex;
            justify-content: space-between;
        }

        .patient-field strong {
            color: var(--medical-blue);
        }

        .consultation-item {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--medical-teal);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .consultation-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .consultation-header {
            color: var(--medical-blue);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .consultation-details {
            display: grid;
            gap: 0.75rem;
        }

        .consultation-field {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 1rem;
        }

        .consultation-field strong {
            color: var(--medical-purple);
            font-size: 0.9rem;
        }

        .consultation-field span {
            color: var(--medical-gray);
            line-height: 1.5;
        }

        .alert-no-patient {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            border: none;
            border-radius: 12px;
            color: #d63031;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-badge.online {
            background: rgba(40, 167, 69, 0.1);
            color: var(--medical-green);
        }

        .d-none { display: none !important; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 1rem; }
            .main-cards { grid-template-columns: 1fr; }
            .dashboard-metrics { grid-template-columns: 1fr; }
            .patient-info { grid-template-columns: 1fr; }
            .consultation-field { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <h3>MyHealth</h3>
            </div>
            <div class="doctor-profile">
                <img src="../uploads/fotos_perfil/<?= htmlspecialchars($dadosMedico['foto_perfil'] ?: 'default-paciente.png') ?>" class="profile-img" alt="Foto de perfil">
                <div class="profile-name">Dr. <?php echo htmlspecialchars($dadosMedico['nome']); ?></div>
                <div class="doctor-crm">CRM: <?php echo htmlspecialchars($dadosMedico['crm']); ?></div>
            </div>
        </div>

        <div class="nav-section">
            <a href="#" class="nav-link active">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>
            <a href="consulta.php" class="nav-link">
                <i class="fas fa-user-md"></i>
                Nova Consulta
            </a>
            <a href="prontuario.php" class="nav-link">
                <i class="fas fa-notes-medical"></i>
                Prontuários
            </a>
            <a href="configuracao_medico.php" class="nav-link">
                <i class="fas fa-user-cog"></i>
                Configurações
            </a>
        </div>

        <form action="../controller/logout.php" method="post">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Encerrar Sessão
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content container-fluid">
        <!-- Header Section -->
        <div class="header-section mx-auto" >
            <h1 class="page-title text-center">
                <i class="fas fa-stethoscope"></i>
                Bem-vindo, Dr. <?php echo htmlspecialchars($dadosMedico['nome']); ?>
            </h1>
            <p class="page-subtitle">Gerencie seus atendimentos, consulte prontuários e acompanhe suas atividades médicas de forma eficiente e segura.</p>
            
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge online">
                    <i class="fas fa-shield-alt"></i>
                    Sistema Médico Seguro
                </span>
                <span class="text-muted">
                    <i class="fas fa-sync-alt me-1"></i>
                    Atualizado em tempo real
                </span>
            </div>
        </div>

        <!-- Dashboard Metrics -->
        <div class="dashboard-metrics">
            <div class="metric-card">
                <div class="metric-icon today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="metric-info">
                    <h3 id="atendimentos-dia">0</h3>
                    <p>Atendimentos Hoje</p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon month">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="metric-info">
                    <h3 id="atendimentos-mes">0</h3>
                    <p>Atendimentos no Mês</p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon year">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="metric-info">
                    <h3 id="atendimentos-ano">0</h3>
                    <p>Atendimentos no Ano</p>
                </div>
            </div>
        </div>

        <!-- Main Cards -->
        <div class="main-cards">
            <!-- Doctor Info Card -->
            <div class="info-card">
                <div class="card-header" style="max-width: 12000px;">
                    <i class="fas fa-user-md"></i>
                    Suas Informações Profissionais
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Telefone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['telefone']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Endereço:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['endereco']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data de Nascimento:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['data_nascimento']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ano de Formação:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['ano_formacao']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value"><?php echo htmlspecialchars($dadosMedico['status_atual']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>