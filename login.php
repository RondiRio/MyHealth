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
    <!-- Formulário de Login -->
    <div class="row d-flex flex-column justify-content-center mt-4 card-body">
            <div class="col md-5 mt-2">
                <div class="card login-card">
                    <div class="card-header text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="">
                        <form action="Valida_login.php" method="POST">
                            <div class="mb-3">
                                <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                                <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                    <option value="medico">Médico</option>
                                    <option value="paciente">Paciente</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="identificador" class="form-label">CRM ou CPF</label>
                                <input type="password" id="identificador" name="identificador" class="form-control" required placeholder="Digite seu CRM (Médico) ou CPF (Paciente)" autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" id="senha" name="senha" class="form-control" required placeholder="Digite sua senha" autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <p class="text-center mt-3">Não tem uma conta? <a href="cadastrar.php">Cadastre-se aqui</a></p>
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
