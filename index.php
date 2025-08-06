<?php
require_once 'iniciar.php';

if (isset($_SESSION['usuario_id'])) {
    $dashboard = ($_SESSION['tipo_usuario'] === 'medico') ? 'dashboard_medico.php' : 'dashBoard_Paciente.php';
    header("Location: " . $dashboard);
    exit;
}

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyHealth - Plataforma de Saúde Preventiva</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link rel="stylesheet" href="css/cabecalhoCSS.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-primary-rgb: 78, 115, 223; /* Cor primária (azul) */
            --bs-secondary-rgb: 134, 142, 150; /* Cor secundária (cinza) */
            --light-blue-bg: #f1f5f9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
        }

        .section {
            padding: 60px 0;
        }
        .section-bg {
            background-color: var(--light-blue-bg);
        }

        .hero-section {
            background: linear-gradient(45deg, rgba(var(--bs-primary-rgb), 0.9), rgba(var(--bs-primary-rgb), 0.7)), url('images/background.jpg') center center no-repeat;
            background-size: cover;
            color: white;
        }
        .hero-section h1 {
            font-weight: 700;
        }

        .health-tip-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .health-tip-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(var(--bs-primary-rgb), 0.2);
        }
        .health-tip-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .carousel-item img {
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
        }
        .carousel-caption {
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            border-radius: 0 0 15px 15px;
        }
        
        .login-section {
            background-color: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
    <header>
       <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.jpg" alt="Logo MyHealth" width="150" height="75" class="d-inline-block align-text-top">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="servicos.php">Serviços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sobre.php">Sobre Nós</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contato.php">Contato</a>
                </li>
            </ul>
            
            <div class="d-flex gap-2">
                <a href="login.php" class="btn btn-outline-light">Login</a>
                <a href="cadastrar.php" class="btn btn-light">Cadastre-se</a>
            </div>
            
        </div>
    </div>
</nav>
    </header>

    <section class="hero-section text-center py-5">
        <div class="container">
            <h1 class="display-4 mt-4">Conectando Saúde, Cuidando de Vidas</h1>
            <p class="lead col-md-8 mx-auto">A plataforma definitiva para médicos e pacientes gerenciarem a saúde preventiva com inteligência, segurança e praticidade.</p>
        </div>
    </section>

    <section id="dicas" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Dicas para uma Vida Saudável</h2>
                <p class="lead text-muted">Pequenas mudanças, grandes resultados. Comece hoje.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Alimentação saudável.jpg" class="card-img-top" alt="Alimentação Saudável">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">Alimentação Saudável</h5>
                            <p class="card-text">Descubra alimentos que fortalecem seu sistema imunológico e previnem doenças crônicas.</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">Leia mais</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Exercicios fisicos.jpg" class="card-img-top" alt="Exercícios Físicos">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">Exercícios Físicos</h5>
                            <p class="card-text">Dicas de atividades para manter um estilo de vida ativo e combater o sedentarismo.</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">Leia mais</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Saude mental.jpg" class="card-img-top" alt="Saúde Mental">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">Saúde Mental</h5>
                            <p class="card-text">Estratégias para reduzir o estresse, melhorar o bem-estar e cuidar da sua mente.</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">Leia mais</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<section id="login" class="login-section section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card border-0">
                        <div class="card-header text-center bg-dark text-white">
                            <h3 class="mb-0">Acesse sua Conta</h3>
                        </div>
                        <div class="card-body p-4">

                            <?php if ($notificacao): ?>
                                <div class="alert <?= $notificacao['tipo'] === 'sucesso' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                    <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="login.php" method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                                <button type="submit" class="btn btn-primary w-100 mt-3">Entrar</button>
                            </form>
                             <p class="text-center mt-3 mb-0">Não tem uma conta? <a href="cadastrar.php" class="fw-bold">Cadastre-se</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="recursos" class="section section-bg">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Recursos Inovadores</h2>
                <p class="lead text-muted">Ferramentas projetadas para sua conveniência e segurança.</p>
            </div>
            <div id="carouselRecursos" class="carousel slide shadow-lg" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/Prontuário Digital.jpg" class="d-block w-100" alt="Prontuário Digital">
                        <div class="carousel-caption d-none d-md-block pb-3">
                            <h5>Prontuário Digital Unificado</h5>
                            <p>Acesse e atualize o histórico completo do paciente em tempo real, de qualquer lugar.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="images/Lembretes de Consultas.jpg" class="d-block w-100" alt="Lembretes de Consultas">
                        <div class="carousel-caption d-none d-md-block pb-3">
                            <h5>Agendamento e Lembretes Inteligentes</h5>
                            <p>Nunca mais esqueça uma consulta. Nosso sistema notifica médicos e pacientes automaticamente.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="images/Comunicação segura.jpg" class="d-block w-100" alt="Comunicação Segura">
                        <div class="carousel-caption d-none d-md-block pb-3">
                            <h5>Comunicação Segura e Direta</h5>
                            <p>Um canal de mensagens criptografado para uma comunicação eficiente e sigilosa entre médico e paciente.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselRecursos" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselRecursos" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </section>
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> MyHealth Plataforma de Saúde | <a href="mailto:suporte@myhealth.com" class="text-white">suporte@myhealth.com</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>