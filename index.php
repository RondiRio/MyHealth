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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #059669;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #ffffff;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand img {
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary-color);
            border-color: white;
        }

        .btn-light {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background: transparent;
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 50%, #1e40af 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-section .lead {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.95;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Sections */
        .section {
            padding: 5rem 0;
        }

        .section-bg {
            background-color: var(--light-bg);
        }

        /* Health Tips Cards */
        .health-tip-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
        }

        .health-tip-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .health-tip-card .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .health-tip-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .health-tip-card .card-body {
            padding: 2rem;
        }

        .health-tip-card .card-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .health-tip-card .card-text {
            color: var(--secondary-color);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Login Section */
        .login-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
        }

        .login-section .card {
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow-hover);
        }

        .login-section .card-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            border-bottom: none;
        }

        .login-section .card-body {
            padding: 2rem;
        }

        /* Carousel */
        .carousel {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow-hover);
        }

        .carousel-item img {
            height: 500px;
            object-fit: cover;
        }

        .carousel-caption {
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            border-radius: 0;
            padding: 2rem;
        }

        .carousel-caption h5 {
            font-weight: 600;
            font-size: 1.5rem;
        }

        /* Buttons */
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 3rem 0;
        }

        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: white;
        }

        /* Section Titles */
        .section h2 {
            font-weight: 700;
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .section .lead {
            font-size: 1.1rem;
            color: var(--secondary-color);
            font-weight: 400;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .health-tip-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .health-tip-card:nth-child(1) { animation-delay: 0.1s; }
        .health-tip-card:nth-child(2) { animation-delay: 0.2s; }
        .health-tip-card:nth-child(3) { animation-delay: 0.3s; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section .lead {
                font-size: 1.1rem;
            }
            
            .section {
                padding: 3rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo.jpg" alt="Logo MyHealth" width="140" height="70" class="d-inline-block align-text-top">
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">
                                <i class="fas fa-home me-2"></i>Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="servicos.php">
                                <i class="fas fa-stethoscope me-2"></i>Serviços
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobre.php">
                                <i class="fas fa-info-circle me-2"></i>Sobre Nós
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contato.php">
                                <i class="fas fa-envelope me-2"></i>Contato
                            </a>
                        </li>
                    </ul>
                    
                    <div class="d-flex gap-2">
                        <a href="login.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="cadastrar.php" class="btn btn-light">
                            <i class="fas fa-user-plus me-2"></i>Cadastre-se
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content text-center text-white">
                <h1 class="display-4">Conectando Saúde, Cuidando de Vidas</h1>
                <p class="lead col-lg-8 mx-auto mb-4">A plataforma definitiva para médicos e pacientes gerenciarem a saúde preventiva com inteligência, segurança e praticidade.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="cadastrar.php" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i>Comece Agora
                    </a>
                    <a href="#dicas" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-arrow-down me-2"></i>Saiba Mais
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Health Tips Section -->
    <section id="dicas" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Dicas para uma Vida Saudável</h2>
                <p class="lead">Pequenas mudanças, grandes resultados. Comece hoje.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Alimentação saudável.jpg" class="card-img-top" alt="Alimentação Saudável">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <i class="fas fa-apple-alt text-success me-2"></i>Alimentação Saudável
                            </h5>
                            <p class="card-text">Descubra alimentos que fortalecem seu sistema imunológico e previnem doenças crônicas.</p>
                            <a href="servicos.php#nutricao" class="btn btn-outline-primary mt-auto">
                                <i class="fas fa-arrow-right me-2"></i>Leia mais
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Exercicios fisicos.jpg" class="card-img-top" alt="Exercícios Físicos">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <i class="fas fa-dumbbell text-primary me-2"></i>Exercícios Físicos
                            </h5>
                            <p class="card-text">Dicas de atividades para manter um estilo de vida ativo e combater o sedentarismo.</p>
                            <a href="servicos.php#exercicios" class="btn btn-outline-primary mt-auto">
                                <i class="fas fa-arrow-right me-2"></i>Leia mais
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card health-tip-card h-100">
                        <img src="images/Saude mental.jpg" class="card-img-top" alt="Saúde Mental">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <i class="fas fa-brain text-info me-2"></i>Saúde Mental
                            </h5>
                            <p class="card-text">Estratégias para reduzir o estresse, melhorar o bem-estar e cuidar da sua mente.</p>
                            <a href="servicos.php#saude-mental" class="btn btn-outline-primary mt-auto">
                                <i class="fas fa-arrow-right me-2"></i>Leia mais
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section id="login" class="login-section section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card">
                        <div class="card-header text-center text-white">
                            <h3 class="mb-0">
                                <i class="fas fa-user-circle me-2"></i>Acesse sua Conta
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($notificacao): ?>
                                <div class="alert <?= $notificacao['tipo'] === 'sucesso' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                    <i class="fas fa-<?= $notificacao['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                                    <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="login.php" method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                </button>
                            </form>
                            <p class="text-center mt-3 mb-0">
                                Não tem uma conta? 
                                <a href="cadastrar.php" class="text-primary fw-bold text-decoration-none">Cadastre-se</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Carousel Section -->
    <section id="recursos" class="section section-bg">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Recursos Inovadores</h2>
                <p class="lead">Ferramentas projetadas para sua conveniência e segurança.</p>
            </div>
            <div id="carouselRecursos" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselRecursos" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#carouselRecursos" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#carouselRecursos" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/Prontuário Digital.jpg" class="d-block w-100" alt="Prontuário Digital">
                        <div class="carousel-caption">
                            <h5><i class="fas fa-file-medical me-2"></i>Prontuário Digital Unificado</h5>
                            <p>Acesse e atualize o histórico completo do paciente em tempo real, de qualquer lugar.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="images/Lembretes de Consultas.jpg" class="d-block w-100" alt="Lembretes de Consultas">
                        <div class="carousel-caption">
                            <h5><i class="fas fa-calendar-alt me-2"></i>Agendamento e Lembretes Inteligentes</h5>
                            <p>Nunca mais esqueça uma consulta. Nosso sistema notifica médicos e pacientes automaticamente.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="images/Comunicação segura.jpg" class="d-block w-100" alt="Comunicação Segura">
                        <div class="carousel-caption">
                            <h5><i class="fas fa-shield-alt me-2"></i>Comunicação Segura e Direta</h5>
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

    <!-- Footer -->
    <footer class="text-white text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <h5 class="fw-bold mb-3">
                        <img src="images/logo.jpg" alt="MyHealth" width="40" height="40" class="rounded me-2">
                        MyHealth
                    </h5>
                    <p class="mb-3">Conectando saúde, cuidando de vidas. Sua plataforma de saúde preventiva.</p>
                    <p class="mb-0">
                        <i class="fas fa-copyright me-1"></i>
                        <?= date('Y') ?> MyHealth Plataforma de Saúde | 
                        <a href="mailto:suporte@myhealth.com">
                            <i class="fas fa-envelope me-1"></i>suporte@myhealth.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-hide navbar on scroll
        let lastScrollTop = 0;
        const navbar = document.querySelector('.navbar');
        
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            lastScrollTop = scrollTop;
        });
    </script>
</body>
</html>