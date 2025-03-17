<!DOCTYPE html>
<?print_r($_SESSION)?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Plataforma de Saúde</title>
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

    <!-- Formulário de Cadastro -->
    <div class="row d-flex flex-column justify-content-center mt-4 card-body">
        <div class="col md-5 mt-2">
            <div class="card login-card">
                <div class="card-header text-center">
                    <h3>Cadastre-se</h3>
                </div>
                <div class="card-body">
                    <form action="Valida_cadastro.php" method="POST">
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Você é Médico ou Paciente?</label>
                            <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                <option value="medico">Médico</option>
                                <option value="paciente">Paciente</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="identificador" class="form-label">CRM (Médico) ou CPF (Paciente)</label>
                            <input type="password" id="identificador" name="identificador" class="form-control" required placeholder="Digite seu CRM ou CPF" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" id="senha" name="senha" class="form-control" required placeholder="Digite sua senha" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirme sua senha</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required placeholder="Digite novamente sua senha" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" required placeholder="Digite seu e-mail">
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" required placeholder="Digite seu telefone">
                        </div>

                        <div class="mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" id="cep" name="cep" class="form-control" required placeholder="Digite seu CEP" onblur="buscarEndereco()">
                        </div>

                        <div class="mb-3">
                            <label for="rua" class="form-label">Rua</label>
                            <input type="text" id="rua" name="rua" class="form-control" required placeholder="Rua" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" id="numero" name="numero" class="form-control" required placeholder="Número">
                        </div>

                        <div class="mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Complemento (Opcional)">
                        </div>

                        <div class="mb-3">
                            <label for="bloco" class="form-label">Bloco</label>
                            <input type="text" id="bloco" name="bloco" class="form-control" placeholder="Bloco (Opcional)">
                        </div>

                        <div class="mb-3">
                            <label for="apartamento" class="form-label">Apartamento</label>
                            <input type="text" id="apartamento" name="apartamento" class="form-control" placeholder="Apartamento (Opcional)">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                    </form>

                    <p class="text-center mt-3">Já tem uma conta? <a href="login.php">Faça login</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para buscar endereço via CEP -->
    <script>
        function buscarEndereco() {
            let cep = document.getElementById('cep').value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert("CEP inválido! Digite 8 números.");
                return;
            }

            let url = `https://viacep.com.br/ws/${cep}/json/`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                    } else {
                        alert("CEP não encontrado!");
                    }
                })
                .catch(error => console.error("Erro ao buscar o CEP:", error));
        }
    </script>

    <!-- Script do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Rodapé -->
    <footer class="bg-dark text-white text-center p-3 mt-5">
        <div class="container">
            <h2>Baixe Nosso Aplicativo</h2>
            <p>Tenha acesso ao seu histórico de saúde a qualquer momento pelo celular.</p>
            <a href="#" class="btn btn-dark"><i class="fab fa-android"></i> Baixar para Android</a>
            <a href="#" class="btn btn-dark"><i class="fab fa-apple"></i> Baixar para iOS</a>
        </div>

        <p>&copy; 2025 Plataforma de Saúde | <a href="mailto:suporte@plataformasaude.com" class="text-white">suporte@plataformasaude.com</a></p>
    </footer>
</body>
</html>
