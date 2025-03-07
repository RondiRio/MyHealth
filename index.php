<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Gestão de Saúde Preventiva</title>
    <!-- Link para o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="routes/cssRoutes/cabecalhoCSS.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .login-card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include_once("routes/header.phtml")?>

    <div class="container min-vh-100 d-flex flex-column justify-content-center mb-5 mt-5">
        <div class="row align-items-center">
            <!-- Apresentação -->
            <div class="col-md-6">
                <div class="bg-primary text-white p-4 rounded" style="max-width: 600px;">
                    <h1>Bem-vindo à Plataforma de Gestão de Saúde Preventiva</h1>
                    <p class="lead">Este sistema oferece aos médicos e pacientes uma forma fácil de acompanhar a saúde, visualizar prontuários, históricos e muito mais.</p>
                    <p>Faça login para acessar as funcionalidades específicas para médicos ou pacientes.</p>
                </div>
            </div>

            <!-- Imagem -->
            <div class="col-md-6 text-center">
                <img src="images/doutor celular.jpg" alt="Médico atendendo paciente pelo celular" class="img-fluid rounded" style="border: 5px solid #FFF; max-width: 92%;">
            </div>
        </div>

        <!-- informações importantes -->
        <div class="container mt-5">
            <div class="row text-center bg-white">
                <div class="col-md-4 ">
                    <i class="fas fa-user-md fa-3x text-primary"></i>
                    <h3>Médicos</h3>
                    <p>Facilidade no acompanhamento dos pacientes com prontuários digitais e alertas de consultas.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-user-injured fa-3x text-primary"></i>
                    <h3>Pacientes</h3>
                    <p>Monitore sua saúde, receba lembretes de consultas e acompanhe seu histórico médico.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    <h3>Segurança</h3>
                    <p>Armazenamos os dados de forma segura, garantindo sigilo e confiabilidade.</p>
                </div>
            </div>
        </div>
    </div>
<!-- Depoimentos -->
    <div class="container mt-5 bg-white">
        <h2 class="text-center">Por que escolher nossa plataforma?</h2>
        <div class="row text-center ">
            <div class="col-md-4">
                <i class="fas fa-clock fa-3x text-primary"></i>
                <h4>Praticidade</h4>
                <p>Gerencie sua saúde com poucos cliques, acesso rápido e fácil.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-file-medical fa-3x text-primary"></i>
                <h4>Histórico Completo</h4>
                <p>Acompanhe consultas, exames e tratamentos em um só lugar.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-lock fa-3x text-primary"></i>
                <h4>Segurança</h4>
                <p>Seus dados são criptografados e protegidos contra acessos não autorizados.</p>
            </div>
        </div>
    </div>
    <!-- recursos -->
    <div class="container mt-5 bg-white">
        <h2 class="text-center">Recursos da Plataforma</h2>
        <div id="carouselRecursos" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/Prontuário Digital.jpg" class="d-block w-100" alt="Prontuário Digital">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Prontuário Digital</h5>
                        <p>Acesse e atualize o histórico médico em tempo real.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/Lembretes de Consultas.jpg" class="d-block w-100" alt="Lembretes de Consultas">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Lembretes de Consultas</h5>
                        <p>Receba notificações automáticas para suas consultas e exames.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/Comunicação segura.jpg" class="d-block w-100" alt="Comunicação Segura">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Comunicação Segura</h5>
                        <p>Médicos e pacientes podem se comunicar de forma segura e eficiente.</p>
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
    <!-- blog -->
    <div class="container mt-5">
        <h2 class="text-center">Dicas de Saúde</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="images/Alimentação saudável.jpg" class="card-img-top" alt="Alimentação saudável">
                    <div class="card-body">
                        <h5 class="card-title">Alimentação Saudável</h5>
                        <p class="card-text">Descubra quais alimentos ajudam na prevenção de doenças.</p>
                        <a href="#" class="btn btn-primary">Leia mais</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/Exercicios fisicos.jpg" class="card-img-top" alt="Exercícios físicos">
                    <div class="card-body">
                        <h5 class="card-title">Exercícios Físicos</h5>
                        <p class="card-text">Dicas de atividades para manter um estilo de vida ativo.</p>
                        <a href="#" class="btn btn-primary">Leia mais</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/Saude mental.jpg" class="card-img-top" alt="Saúde mental">
                    <div class="card-body">
                        <h5 class="card-title">Saúde Mental</h5>
                        <p class="card-text">Estratégias para reduzir o estresse e melhorar o bem-estar.</p>
                        <a href="#" class="btn btn-primary">Leia mais</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!--depoimentos-->
    <div class="container mt-5">
        <h2 class="text-center">O que nossos usuários dizem</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card p-3">
                    <p>"A plataforma facilitou muito meu trabalho. Agora tenho acesso rápido ao histórico dos pacientes!"</p>
                    <h5 class="text-primary">— Dr. Marcos Almeida</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <p>"Posso visualizar todas as minhas consultas e exames em um só lugar. Muito prático!"</p>
                    <h5 class="text-primary">— Ana Souza, paciente</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- perguntas frequentes -->

    <div class="container mt-5">
        <h2 class="text-center">Perguntas Frequentes</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Como faço para me cadastrar?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Basta clicar em "Cadastre-se aqui" e preencher seus dados conforme seu perfil (Médico ou Paciente).
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        O sistema é seguro?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sim, utilizamos criptografia e proteção de dados para garantir total segurança.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Formulário de Login -->
    <div class="row d-flex flex-column justify-content-center mt-4 card-body">
            <div class="col md-5 mt-2">
                <div class="card login-card">
                    <div class="card-header text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="">
                        <form action="routes/login.php" method="POST">
                            <div class="mb-3">
                                <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                                <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                    <option value="medico">Médico</option>
                                    <option value="paciente">Paciente</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="identificador" class="form-label">CRM ou CPF</label>
                                <input type="number" id="identificador" name="identificador" class="form-control" required placeholder="Digite seu CRM (Médico) ou CPF (Paciente)" autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" id="senha" name="senha" class="form-control" required placeholder="Digite sua senha" autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <p class="text-center mt-3">Não tem uma conta? <a href="routes/register.php">Cadastre-se aqui</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- Script do Bootstrap -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

      <!-- rodapé -->
    <footer class="bg-dark text-white text-center p-3 mt-5">
        <div class="container text-center mt-5">
            <h2>Baixe Nosso Aplicativo</h2>
            <p>Tenha acesso ao seu histórico de saúde a qualquer momento pelo celular.</p>
            <a href="#" class="btn btn-dark"><i class="fab fa-android"></i> Baixar para Android</a>
            <a href="#" class="btn btn-dark"><i class="fab fa-apple"></i> Baixar para iOS</a>
        </div>

        <p>&copy; 2025 Plataforma de Saúde | <a href="mailto:suporte@plataformasaude.com" class="text-white">suporte@plataformasaude.com</a></p>
    </footer>
</body>
</html>
