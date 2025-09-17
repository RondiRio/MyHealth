<?php
require_once '../controller/iniciar.php';
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

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .footer .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .footer .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
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

        /* Placeholder Images */
        .team-placeholder {
            background: linear-gradient(45deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
            font-size: 3rem;
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

<?= include_once('../routes/header.phtml')?>

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
                        <span class="stats-number" data-target="50000">25.874</span>
                        <div class="stats-label">Pacientes Atendidos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number " data-target="1200">5.287</span>
                        <div class="stats-label">Médicos Parceiros</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <span class="stats-number" data-target="200000">29.238</span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section section-bg">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nossos Valores</h2>
                <p class="lead col-lg-6 mx-auto">Os princípios que guiam cada decisão e ação em nossa jornada para transformar a saúde digital.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Transparência</h4>
                        <p>Comunicação clara e honesta em todos os processos, construindo confiança através da transparência total.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Excelência</h4>
                        <p>Busca constante pela qualidade superior em cada serviço, produto e interação com nossos usuários.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Inovação</h4>
                        <p>Desenvolvimento contínuo de soluções criativas e tecnológicas para os desafios da saúde moderna.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Compromisso</h4>
                        <p>Dedicação total ao bem-estar de nossos usuários, com responsabilidade social e ética profissional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nossa Equipe</h2>
                <p class="lead col-lg-6 mx-auto">Profissionais apaixonados e dedicados que tornam o MyHealth uma realidade.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-card-body">
                            <div class="team-role">CEO & Fundador</div>
                            <h4 class="team-name">Rondineli Oliveira</h4>
                            <p class="team-bio">Ceo e fundados da NetoNerd (empresa matriz do projeto MyHealth). Formando em Ciencia da computação pelo UNIFEO, Cientista de dados pela UDEMY. Publicado pela UNIFESO, com o artigo Educação e tecnologia.</p>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-card-body">
                            <div class="team-role">CTO</div>
                            <h4 class="team-name">Ana Santos</h4>
                            <p class="team-bio">Engenheira de software especializada em sistemas de saúde, com expertise em segurança de dados e arquitetura de sistemas complexos.</p>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-card-body">
                            <div class="team-role">Head de Produto</div>
                            <h4 class="team-name">Rafael Costa</h4>
                            <p class="team-bio">Designer de UX/UI com foco em experiência do usuário em aplicações médicas, garantindo interfaces intuitivas e acessíveis.</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="section section-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Nossa História</h2>
                    <p class="lead">Uma jornada de inovação e dedicação em transformar o cuidado com a saúde. Um projeto da empresa NetoNerd</p>
                </div>
                <div class="col-lg-6">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-year">2020</div>
                            <div class="timeline-content">
                                <h4>Fundação</h4>
                                <p>O MyHealth é pautado em uma reunião de um projeto de Startup, onde foi delimitada a sua principal função e alinhados os seus objetivos.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">2024</div>
                            <div class="timeline-content">
                                <h4>Primeiro passo</h4>
                                <p>O projeto foi prototipado e esclolhido para ser objeto de estudo do nosso CEO - Rondineli.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">2025</div>
                            <div class="timeline-content">
                                <h4>Implementação</h4>
                                <p>Com uma pesquisa fundamentada, Rondineli valida a necessidade urgente do MyHealth para melhorar e agilizar a saúde, apresentando seu projeto à UNIFESO.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Faça Parte da Nossa História</h2>
                    <p class="lead mb-4">Junte-se a milhares de pessoas que já escolheram o MyHealth para cuidar da sua saúde de forma moderna, segura e eficiente.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="../publics/cadastrar.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Cadastre-se Agora
                        </a>
                        <a href="../publics/login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>MyHealth</h5>
                    <p class="mb-4">Revolucionando o cuidado com a saúde através da tecnologia, conectando médicos e pacientes em uma plataforma segura e eficiente.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="../index.php">Início</a></li>
                        <li class="mb-2"><a href="sobre.php">Sobre Nós</a></li>
                        <li class="mb-2"><a href="servicos.php">Serviços</a></li>
                        <li class="mb-2"><a href="contato.php">Contato</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Para Médicos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="cadastrar.php">Cadastro</a></li>
                        <li class="mb-2"><a href="login.php">Login</a></li>
                        <li class="mb-2"><a href="#">Portal Médico</a></li>
                        <li class="mb-2"><a href="#">Suporte</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Para Pacientes</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="cadastrar.php">Cadastro</a></li>
                        <li class="mb-2"><a href="login.php">Login</a></li>
                        <li class="mb-2"><a href="#">Agendar Consulta</a></li>
                        <li class="mb-2"><a href="#">Meu Histórico</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Suporte</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="#">Política de Privacidade</a></li>
                        <li class="mb-2"><a href="#">Termos de Uso</a></li>
                        <li class="mb-2"><a href="contato.php">Fale Conosco</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 MyHealth - NetoNerd. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Desenvolvido com <i class="fas fa-heart text-danger"></i> pela equipe NetoNerd</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stats-number[data-target]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 20);
            });
        }

        // Intersection Observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    if (entry.target.querySelector('.stats-number[data-target]')) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, { threshold: 0.1 });