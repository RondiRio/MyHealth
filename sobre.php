<?php
require_once 'iniciar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - MyHealth</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #059669;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 50%, #1e40af 100%);
            color: white;
            padding: 8rem 0 4rem;
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

        /* Sections */
        .section {
            padding: 5rem 0;
        }

        .section-bg {
            background-color: var(--light-bg);
        }

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

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .stats-label {
            color: var(--secondary-color);
            font-weight: 500;
            font-size: 1rem;
        }

        /* Team Cards */
        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .team-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .team-card-body {
            padding: 2rem;
        }

        .team-role {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .team-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0.5rem 0 1rem;
        }

        .team-bio {
            color: var(--secondary-color);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Values Cards */
        .value-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            color: white;
        }

        .value-card:nth-child(1) .value-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .value-card:nth-child(2) .value-icon {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
        }

        .value-card:nth-child(3) .value-icon {
            background: linear-gradient(135deg, var(--warning-color), #f97316);
        }

        .value-card:nth-child(4) .value-icon {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary-color), var(--success-color));
        }

        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            padding-left: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.5rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-color);
        }

        .timeline-year {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .timeline-content h4 {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .timeline-content p {
            color: var(--secondary-color);
            margin-bottom: 0;
        }

        /* Mission Section */
        .mission-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
        }

        /* Buttons */
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

        .stats-card, .team-card, .value-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Counter Animation */
        .counter {
            opacity: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 6rem 0 3rem;
            }
            
            .section {
                padding: 3rem 0;
            }

            .timeline {
                padding-left: 2rem;
            }

            .timeline::before {
                left: 0.5rem;
            }

            .timeline-item::before {
                left: -2rem;
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
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i>Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="servicos.php">
                                <i class="fas fa-stethoscope me-2"></i>Serviços
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="sobre.php">
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
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-4">Sobre o MyHealth</h1>
                <p class="lead col-lg-8 mx-auto">Conheça nossa história, missão e a equipe dedicada em revolucionar o cuidado com a saúde através da tecnologia.</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number counter" data-target="50000">0</span>
                        <div class="stats-label">Pacientes Atendidos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number counter" data-target="1200">0</span>
                        <div class="stats-label">Médicos Parceiros</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number counter" data-target="200000">0</span>
                        <div class="stats-label">Consultas Realizadas</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number">99.8%</span>
                        <div class="stats-label">Satisfação dos Usuários</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Nossa Missão</h2>
                    <p class="lead mb-4">Democratizar o acesso à saúde de qualidade através da tecnologia, conectando médicos e pacientes em uma plataforma segura, eficiente e humanizada.</p>
                    <div class="row mt-5">
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                <h5 class="fw-bold">Cuidado Humanizado</h5>
                                <p>Tecnologia a serviço do cuidado humano e personalizado.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                                <h5 class="fw-bold">Segurança Total</h5>
                                <p>Seus dados protegidos com os mais altos padrões de segurança.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="fas fa-rocket fa-3x text-warning mb-3"></i>
                                <h5 class="fw-bold">Inovação Constante</h5>
                                <p>Sempre evoluindo para oferecer as melhores soluções em saúde.</p>
                            </div>