<?php
require_once 'iniciar.php';

// Redireciona se o usuário já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
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
    <title>Cadastro - MyHealth</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f1f5f9; font-family: 'Poppins', sans-serif; }
        .card-register { border-radius: 1rem; border: none; }
        
        /* Estilos para o validador de senha */
        #password-feedback ul { list-style-type: none; padding-left: 0; font-size: 0.85rem; }
        #password-feedback li { margin-bottom: 0.25rem; transition: color 0.3s; }
        #password-feedback li.valid { color: #198754; } /* Verde */
        #password-feedback li.invalid { color: #dc3545; } /* Vermelho */
        #password-feedback li.valid::before { content: '✓ '; font-weight: bold; }
        #password-feedback li.invalid::before { content: '✗ '; font-weight: bold; }
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
            </div>
        </div>
    </nav>
</header>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-register shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold">Crie sua Conta</h1>
                            <p class="text-muted">É rápido e fácil. Vamos começar.</p>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form id="formCadastro" action="Valida_cadastro.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="tipo_usuario" class="form-label">Você é Médico ou Paciente?</label>
                                    <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                        <option value="paciente">Paciente</option>
                                        <option value="medico">Médico</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="identificador" class="form-label" id="label-identificador">CPF</label>
                                    <input type="text" id="identificador" name="identificador" class="form-control" required placeholder="Digite seu CPF">
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" id="email" name="email" class="form-control" required placeholder="exemplo@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label for="senha" class="form-label">Senha</label>
                                    <input type="password" id="senha" name="senha" class="form-control" required>
                                    <div id="password-feedback" class="mt-2"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmar_senha" class="form-label">Confirme sua senha</label>
                                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                                    <div id="confirm-password-feedback" class="form-text mt-2"></div>
                                </div>

                                <hr class="my-4">

                                <div class="col-md-6">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" id="telefone" name="telefone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cep" class="form-label">CEP</label>
                                    <input type="text" id="cep" name="cep" class="form-control" required>
                                    <div id="cep-feedback" class="form-text"></div>
                                </div>
                                <div class="col-12">
                                    <label for="rua" class="form-label">Rua</label>
                                    <input type="text" id="rua" name="rua" class="form-control" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" id="numero" name="numero" class="form-control" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="complemento" class="form-label">Complemento (Opcional)</label>
                                    <input type="text" id="complemento" name="complemento" class="form-control">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" id="btnCadastrar" class="btn btn-success btn-lg">Finalizar Cadastro</button>
                            </div>
                        </form>
                        <p class="text-center text-muted mt-3 mb-0 small">Já tem uma conta? <a href="index.php" class="fw-bold">Faça login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- ELEMENTOS DO DOM ---
            const tipoUsuarioSelect = document.getElementById('tipo_usuario');
            const identificadorInput = document.getElementById('identificador');
            const labelIdentificador = document.getElementById('label-identificador');
            const senhaInput = document.getElementById('senha');
            const confirmarSenhaInput = document.getElementById('confirmar_senha');
            const passwordFeedbackDiv = document.getElementById('password-feedback');
            const confirmPasswordFeedbackDiv = document.getElementById('confirm-password-feedback');
            let identificadorMask;
            
            // --- LÓGICA DAS MÁSCARAS DINÂMICAS ---
            const mascaras = {
                paciente: { mask: '000.000.000-00', placeholder: 'Digite seu CPF' },
                medico: { mask: '0000000', placeholder: 'Digite seu CRM (somente números)' }
            };

            function aplicarMascara(tipo) {
    if (identificadorMask) identificadorMask.destroy();

    const config = mascaras[tipo];
    identificadorMask = IMask(identificadorInput, config);
    identificadorInput.placeholder = config.placeholder;
    labelIdentificador.textContent = (tipo === 'medico') ? 'CRM' : 'CPF';

    // Remove o campo UF se já existir (para evitar duplicação)
    const ufExistente = document.getElementById('uf');
    if (ufExistente) {
        ufExistente.remove();
    }

    if (tipo === 'medico') {
        const uf_input = document.createElement('select');
        uf_input.setAttribute('id', 'uf');
        uf_input.setAttribute('name', 'uf');
        uf_input.setAttribute('class', 'form-select mt-2');
        uf_input.setAttribute('required', '');
        uf_input.innerHTML = `
            <option value="" disabled selected>Selecione a UF do CRM</option>
            <option value="AC">AC</option>
            <option value="AL">AL</option>
            <option value="AP">AP</option>
            <option value="AM">AM</option>
            <option value="BA">BA</option>
            <option value="CE">CE</option>
            <option value="DF">DF</option>
            <option value="ES">ES</option>
            <option value="GO">GO</option>
            <option value="MA">MA</option>
            <option value="MT">MT</option>
            <option value="MS">MS</option>
            <option value="MG">MG</option>
            <option value="PA">PA</option>
            <option value="PB">PB</option>
            <option value="PR">PR</option>
            <option value="PE">PE</option>
            <option value="PI">PI</option>
            <option value="RJ">RJ</option>
            <option value="RN">RN</option>
            <option value="RS">RS</option>
            <option value="RO">RO</option>
            <option value="RR">RR</option>
            <option value="SC">SC</option>
            <option value="SP">SP</option>
            <option value="SE">SE</option>
            <option value="TO">TO</option>`;
            

        // Insere o campo UF logo após o campo CRM
        identificadorInput.parentNode.appendChild(uf_input);
    } else {
        identificadorInput.setAttribute('maxlength', '14'); // CPF
    }
}


            tipoUsuarioSelect.addEventListener('change', () => aplicarMascara(tipoUsuarioSelect.value));
            aplicarMascara(tipoUsuarioSelect.value);

            // --- LÓGICA DE VALIDAÇÃO DE SENHA EM TEMPO REAL ---
            const requisitosSenha = [
                { regex: /.{8,}/, text: "Pelo menos 8 caracteres" },
                { regex: /[A-Z]/, text: "Uma letra maiúscula" },
                { regex: /[a-z]/, text: "Uma letra minúscula" },
                { regex: /[0-9]/, text: "Pelo menos um número" },
                { regex: /[^A-Za-z0-9]/, text: "Pelo menos um caractere especial (!@#...)" }
            ];

            senhaInput.addEventListener('input', function() {
                const valor = this.value;
                let feedbackHTML = '<ul>';
                requisitosSenha.forEach(req => {
                    const isValid = req.regex.test(valor);
                    feedbackHTML += `<li class="${isValid ? 'valid' : 'invalid'}">${req.text}</li>`;
                });
                feedbackHTML += '</ul>';
                passwordFeedbackDiv.innerHTML = feedbackHTML;
                validarConfirmacaoSenha();
            });

            // --- LÓGICA DE CONFIRMAÇÃO DE SENHA ---
            function validarConfirmacaoSenha() {
                const senha = senhaInput.value;
                const confirmacao = confirmarSenhaInput.value;
                if (confirmacao.length === 0) {
                    confirmPasswordFeedbackDiv.textContent = '';
                    return;
                }
                if (senha === confirmacao) {
                    confirmPasswordFeedbackDiv.textContent = '✓ Senhas coincidem!';
                    confirmPasswordFeedbackDiv.className = 'form-text text-success fw-bold';
                } else {
                    confirmPasswordFeedbackDiv.textContent = '✗ As senhas não conferem.';
                    confirmPasswordFeedbackDiv.className = 'form-text text-danger fw-bold';
                }
            }
            confirmarSenhaInput.addEventListener('input', validarConfirmacaoSenha);
            
            // --- LÓGICA DA BUSCA DE ENDEREÇO (VIA CEP) ---
            const cepInput = document.getElementById('cep');
            const cepFeedback = document.getElementById('cep-feedback');
            cepInput.addEventListener('blur', function() {
                const cep = cepInput.value.replace(/\D/g, '');
                if (cep.length !== 8) return;
                cepFeedback.textContent = 'Buscando...';
                cepFeedback.classList.remove('text-danger');
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            cepFeedback.textContent = 'CEP não encontrado.';
                            cepFeedback.classList.add('text-danger');
                            document.getElementById('rua').value = '';
                        } else {
                            cepFeedback.textContent = '';
                            document.getElementById('rua').value = data.logradouro;
                            document.getElementById('numero').focus();
                        }
                    })
                    .catch(error => {
                        console.error("Erro ao buscar o CEP:", error);
                        cepFeedback.textContent = 'Erro ao buscar CEP.';
                        cepFeedback.classList.add('text-danger');
                    });
            });
        });
    </script>
</body>
</html>