<?php
require_once 'iniciar.php';

$notificacao = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - MyHealth</title>
    
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

        /* Contact Cards */
        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .contact-icon {
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

        .contact-card:nth-child(1) .contact-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .contact-card:nth-child(2) .contact-icon {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
        }

        .contact-card:nth-child(3) .contact-icon {
            background: linear-gradient(135deg, var(--warning-color), #f97316);
        }

        .contact-card:nth-child(4) .contact-icon {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        /* Form Styles */
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: var(--card-shadow-hover);
            border: none;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
        }

        .form-label i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            font-weight: 600;
            padding: 0.875rem 2.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
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

        /* Map Container */
        .map-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            height: 400px;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        /* FAQ Section */
        .accordion {
            border-radius: 16px;
            overflow: hidden;
            border: none;
        }

        .accordion-item {
            border: none;
            margin-bottom: 1rem;
        }

        .accordion-button {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            color: #374151;
            padding: 1.25rem 1.5rem;
        }

        .accordion-button:not(.collapsed) {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .accordion-body {
            background: white;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 1.5rem;
            color: var(--secondary-color);
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

        .contact-card, .form-card {
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

            .form-card {
                padding: 2rem;
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
                            <a class="nav-link" href="sobre.php">
                                <i class="fas fa-info-circle me-2"></i>Sobre Nós
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contato.php">
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
                <h1 class="display-4 fw-bold mb-4">Entre em Contato</h1>
                <p class="lead col-lg-8 mx-auto">Estamos aqui para ajudar! Entre em contato conosco para esclarecer dúvidas, solicitar suporte ou conhecer melhor nossos serviços.</p>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="section">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4 class="fw-bold mb-3">E-mail</h4>
                        <p class="text-muted mb-3">Resposta em até 24 horas</p>
                        <a href="mailto:suporte@myhealth.com" class="text-primary fw-semibold text-decoration-none">
                            suporte@myhealth.com
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Telefone</h4>
                        <p class="text-muted mb-3">Seg à Sex, 8h às 18h</p>
                        <a href="tel:+5511999999999" class="text-primary fw-semibold text-decoration-none">
                            (11) 99999-9999
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Chat Online</h4>
                        <p class="text-muted mb-3">Disponível 24/7</p>
                        <button class="btn btn-outline-primary btn-sm">
                            Iniciar Chat
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Endereço</h4>
                        <p class="text-muted mb-0">
                            Av. Paulista, 1000<br>
                            São Paulo, SP<br>
                            01310-100
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="section section-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="mb-4">
                            <h2 class="fw-bold mb-3">Envie sua Mensagem</h2>
                            <p class="text-muted">Preencha o formulário abaixo e nossa equipe entrará em contato o mais breve possível.</p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert <?= $notificacao['tipo'] === 'sucesso' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?= $notificacao['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form id="contactForm" method="POST" action="processar_contato.php">
                            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nome" class="form-label">
                                        <i class="fas fa-user"></i>Nome Completo
                                    </label>
                                    <input type="text" id="nome" name="nome" class="form-control" required placeholder="Seu nome completo">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>E-mail
                                    </label>
                                    <input type="email" id="email" name="email" class="form-control" required placeholder="seu@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefone" class="form-label">
                                        <i class="fas fa-phone"></i>Telefone
                                    </label>
                                    <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(11) 99999-9999">
                                </div>
                                <div class="col-md-6">
                                    <label for="assunto" class="form-label">
                                        <i class="fas fa-tag"></i>Assunto
                                    </label>
                                    <select id="assunto" name="assunto" class="form-select" required>
                                        <option value="" disabled selected>Selecione um assunto</option>
                                        <option value="suporte_tecnico">Suporte Técnico</option>
                                        <option value="duvidas_servicos">Dúvidas sobre Serviços</option>
                                        <option value="sugestoes">Sugestões</option>
                                        <option value="parcerias">Parcerias</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="mensagem" class="form-label">
                                        <i class="fas fa-comment-alt"></i>Mensagem
                                    </label>
                                    <textarea id="mensagem" name="mensagem" class="form-control" required placeholder="Descreva sua dúvida ou necessidade..." rows="5"></textarea>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="map-container">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <h5 class="fw-bold">Nossa Localização</h5>
                            <p>Av. Paulista, 1000<br>São Paulo, SP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Perguntas Frequentes</h2>
                <p class="lead">Encontre respostas para as dúvidas mais comuns sobre o MyHealth</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Como faço para me cadastrar no MyHealth?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O cadastro é muito simples! Clique em "Cadastre-se" no menu superior, escolha se você é médico ou paciente, e preencha o formulário com suas informações. Todo o processo leva menos de 5 minutos.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    O MyHealth é seguro?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim! Utilizamos criptografia de ponta a ponta e seguimos todos os padrões internacionais de segurança em saúde digital. Seus dados estão protegidos com a mesma segurança usada por bancos.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    Posso usar o MyHealth pelo celular?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Claro! Nossa plataforma é totalmente responsiva e funciona perfeitamente em smartphones, tablets e computadores. Em breve lançaremos nosso aplicativo móvel.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    Quanto custa usar o MyHealth?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oferecemos diferentes planos para atender suas necessidades. Temos um plano básico a partir de R$ 29/mês, com recursos essenciais, e planos mais completos. Confira todos os detalhes na página de Serviços.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                    Como funciona o agendamento de consultas?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Após fazer login, você pode visualizar a agenda dos médicos disponíveis, escolher o horário que melhor se encaixa na sua rotina e agendar sua consulta. Você receberá lembretes automáticos por email e SMS.
                                </div>
                            </div>
                        </div>
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
    <script src="https://unpkg.com/imask"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para telefone
            const telefoneMask = IMask(document.getElementById('telefone'), {
                mask: '(00) 00000-0000'
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

            // Observe all animated elements
            document.querySelectorAll('.contact-card, .form-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Form validation
            const form = document.getElementById('contactForm');
            form.addEventListener('submit', function(e) {
                const nome = document.getElementById('nome').value.trim();
                const email = document.getElementById('email').value.trim();
                const assunto = document.getElementById('assunto').value;
                const mensagem = document.getElementById('mensagem').value.trim();

                if (!nome || !email || !assunto || !mensagem) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return;
                }

                if (mensagem.length < 10) {
                    e.preventDefault();
                    alert('A mensagem deve ter pelo menos 10 caracteres.');
                    return;
                }
            });

            // Chat button functionality
            document.querySelector('.contact-card .btn').addEventListener('click', function() {
                alert('Funcionalidade de chat será implementada em breve!');
            });
        });
    </script>
</body>
</html>