<?php
require_once 'iniciar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - MyHealth</title>
    
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
            --danger-color: #ef4444;
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

        /* Service Cards */
        .service-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .service-card-primary .service-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .service-card-success .service-icon {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
            color: white;
        }

        .service-card-warning .service-icon {
            background: linear-gradient(135deg, var(--warning-color), #f97316);
            color: white;
        }

        .service-card-info .service-icon {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        /* Feature List */
        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .feature-list li i {
            color: var(--success-color);
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Pricing Cards */
        .pricing-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .pricing-card.featured {
            border: 2px solid var(--primary-color);
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'Mais Popular';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 2rem;
            border-radius: 0 0 20px 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .pricing-card .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 2rem 2rem 1rem;
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-period {
            font-size: 1rem;
            color: var(--secondary-color);
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

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
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

        .service-card, .pricing-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 6rem 0 3rem;
            }
            
            .section {
                padding: 3rem 0;
            }

            .pricing-card.featured {
                transform: none;
                margin-top: 2rem;
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
                            <a class="nav-link active" href="servicos.php">
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
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-4">Nossos Serviços</h1>
                <p class="lead col-lg-8 mx-auto">Descubra como o MyHealth pode revolucionar o cuidado com sua saúde através de tecnologia avançada e atendimento humanizado.</p>
            </div>
        </div>
    </section>

    <!-- Main Services Section -->
    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 col-xl-3">
                    <div class="card service-card service-card-primary h-100">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto">
                                <i class="fas fa-file-medical"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Prontuário Digital</h4>
                            <p class="text-muted mb-4">Histórico médico completo e seguro, acessível a qualquer momento para pacientes e médicos autorizados.</p>
                            <ul class="feature-list text-start">
                                <li><i class="fas fa-check"></i>Histórico médico completo</li>
                                <li><i class="fas fa-check"></i>Acesso seguro e criptografado</li>
                                <li><i class="fas fa-check"></i>Sincronização em tempo real</li>
                                <li><i class="fas fa-check"></i>Backup automático</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-xl-3">
                    <div class="card service-card service-card-success h-100">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Agendamento Inteligente</h4>
                            <p class="text-muted mb-4">Sistema automatizado de agendamento com lembretes e confirmações para nunca perder uma consulta.</p>
                            <ul class="feature-list text-start">
                                <li><i class="fas fa-check"></i>Agendamento online 24/7</li>
                                <li><i class="fas fa-check"></i>Lembretes automáticos</li>
                                <li><i class="fas fa-check"></i>Reagendamento fácil</li>
                                <li><i class="fas fa-check"></i>Integração com calendário</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-xl-3">
                    <div class="card service-card service-card-warning h-100">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Comunicação Segura</h4>
                            <p class="text-muted mb-4">Canal direto e criptografado entre médicos e pacientes para esclarecimento de dúvidas e acompanhamento.</p>
                            <ul class="feature-list text-start">
                                <li><i class="fas fa-check"></i>Chat criptografado</li>
                                <li><i class="fas fa-check"></i>Compartilhamento de arquivos</li>
                                <li><i class="fas fa-check"></i>Videochamadas seguras</li>
                                <li><i class="fas fa-check"></i>Histórico de conversas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-xl-3">
                    <div class="card service-card service-card-info h-100">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Monitoramento Saúde</h4>
                            <p class="text-muted mb-4">Acompanhamento contínuo dos indicadores de saúde com alertas preventivos e relatórios detalhados.</p>
                            <ul class="feature-list text-start">
                                <li><i class="fas fa-check"></i>Gráficos evolutivos</li>
                                <li><i class="fas fa-check"></i>Alertas preventivos</li>
                                <li><i class="fas fa-check"></i>Relatórios automáticos</li>
                                <li><i class="fas fa-check"></i>Análise de tendências</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Services Section -->
    <section id="nutricao" class="section section-bg">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">
                        <i class="fas fa-apple-alt text-success me-3"></i>Orientação Nutricional
                    </h2>
                    <p class="lead mb-4">Receba orientações personalizadas sobre alimentação saudável baseadas no seu perfil de saúde e objetivos pessoais.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Planos alimentares personalizados</li>
                                <li><i class="fas fa-check"></i>Receitas saudáveis</li>
                                <li><i class="fas fa-check"></i>Controle de calorias</li>
                                <li><i class="fas fa-check"></i>Acompanhamento nutricional</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Dicas para diabéticos</li>
                                <li><i class="fas fa-check"></i>Orientações para hipertensos</li>
                                <li><i class="fas fa-check"></i>Dietas especiais</li>
                                <li><i class="fas fa-check"></i>Suplementação adequada</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="images/Alimentação saudável.jpg" class="img-fluid rounded-4 shadow-lg" alt="Alimentação Saudável">
                </div>
            </div>
        </div>
    </section>

    <section id="exercicios" class="section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 order-lg-2">
                    <h2 class="fw-bold mb-4">
                        <i class="fas fa-dumbbell text-primary me-3"></i>Programa de Exercícios
                    </h2>
                    <p class="lead mb-4">Planos de exercícios adaptados ao seu condicionamento físico e condições de saúde, sempre com acompanhamento profissional.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Exercícios personalizados</li>
                                <li><i class="fas fa-check"></i>Níveis de dificuldade</li>
                                <li><i class="fas fa-check"></i>Acompanhamento de progresso</li>
                                <li><i class="fas fa-check"></i>Exercícios para casa</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Fisioterapia preventiva</li>
                                <li><i class="fas fa-check"></i>Exercícios cardíacos</li>
                                <li><i class="fas fa-check"></i>Fortalecimento muscular</li>
                                <li><i class="fas fa-check"></i>Flexibilidade e mobilidade</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <img src="images/Exercicios fisicos.jpg" class="img-fluid rounded-4 shadow-lg" alt="Exercícios Físicos">
                </div>
            </div>
        </div>
    </section>

    <section id="saude-mental" class="section section-bg">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">
                        <i class="fas fa-brain text-info me-3"></i>Cuidados com Saúde Mental
                    </h2>
                    <p class="lead mb-4">Ferramentas e orientações para cuidar da sua saúde mental, com técnicas de relaxamento, meditação e acompanhamento psicológico.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Técnicas de relaxamento</li>
                                <li><i class="fas fa-check"></i>Meditação guiada</li>
                                <li><i class="fas fa-check"></i>Controle de estresse</li>
                                <li><i class="fas fa-check"></i>Exercícios de respiração</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Acompanhamento psicológico</li>
                                <li><i class="fas fa-check"></i>Diário de humor</li>
                                <li><i class="fas fa-check"></i>Técnicas de mindfulness</li>
                                <li><i class="fas fa-check"></i>Terapia cognitiva</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="images/Saude mental.jpg" class="img-fluid rounded-4 shadow-lg" alt="Saúde Mental">
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Planos e Preços</h2>
                <p class="lead">Escolha o plano ideal para suas necessidades de saúde</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-header text-center">
                            <h4 class="fw-bold">Básico</h4>
                            <div class="price">R$ 29<span class="price-period">/mês</span></div>
                        </div>
                        <div class="card-body p-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Prontuário digital básico</li>
                                <li><i class="fas fa-check"></i>Agendamento de consultas</li>
                                <li><i class="fas fa-check"></i>Lembretes por email</li>
                                <li><i class="fas fa-check"></i>Chat com médicos (limitado)</li>
                                <li><i class="fas fa-check"></i>Suporte básico</li>
                            </ul>
                            <div class="d-grid mt-4">
                                <a href="cadastrar.php" class="btn btn-outline-primary">Começar Agora</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card pricing-card featured h-100">
                        <div class="card-header text-center">
                            <h4 class="fw-bold">Premium</h4>
                            <div class="price">R$ 79<span class="price-period">/mês</span></div>
                        </div>
                        <div class="card-body p-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Todos os recursos do Básico</li>
                                <li><i class="fas fa-check"></i>Prontuário completo + histórico</li>
                                <li><i class="fas fa-check"></i>Chat ilimitado com médicos</li>
                                <li><i class="fas fa-check"></i>Videochamadas médicas</li>
                                <li><i class="fas fa-check"></i>Monitoramento avançado</li>
                                <li><i class="fas fa-check"></i>Orientações personalizadas</li>
                                <li><i class="fas fa-check"></i>Relatórios detalhados</li>
                                <li><i class="fas fa-check"></i>Suporte prioritário</li>
                            </ul>
                            <div class="d-grid mt-4">
                                <a href="cadastrar.php" class="btn btn-primary">Escolher Premium</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-header text-center">
                            <h4 class="fw-bold">Empresarial</h4>
                            <div class="price">R$ 199<span class="price-period">/mês</span></div>
                        </div>
                        <div class="card-body p-4">
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>Todos os recursos Premium</li>
                                <li><i class="fas fa-check"></i>Até 50 funcionários</li>
                                <li><i class="fas fa-check"></i>Dashboard empresarial</li>
                                <li><i class="fas fa-check"></i>Relatórios coletivos</li>
                                <li><i class="fas fa-check"></i>Medicina ocupacional</li>
                                <li><i class="fas fa-check"></i>Campanhas de vacinação</li>
                                <li><i class="fas fa-check"></i>Gerente de conta dedicado</li>
                                <li><i class="fas fa-check"></i>Integração com RH</li>
                            </ul>
                            <div class="d-grid mt-4">
                                <a href="contato.php" class="btn btn-outline-primary">Falar com Vendas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Pronto para Revolucionar sua Saúde?</h2>
                    <p class="lead mb-4">Junte-se a milhares de pacientes e médicos que já confiam no MyHealth para cuidar da saúde de forma inteligente e preventiva.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="cadastrar.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket me-2"></i>Começar Gratuitamente
                        </a>
                        <a href="contato.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-phone me-2"></i>Falar com Especialista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-5">
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
                        <a href="mailto:suporte@myhealth.com" class="text-white text-decoration-none">
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

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.service-card, .pricing-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>