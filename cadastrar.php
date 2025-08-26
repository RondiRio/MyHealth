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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body { 
            background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
            font-family: 'Inter', sans-serif;
            color: #334155;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            backdrop-filter: blur(10px);
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

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .card-register { 
            border-radius: 24px; 
            border: none;
            box-shadow: var(--card-shadow);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
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
            margin-bottom: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.875rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        /* Estilos para o validador de senha */
        #password-feedback ul { 
            list-style-type: none; 
            padding-left: 0; 
            font-size: 0.85rem; 
            margin-top: 0.5rem;
        }
        #password-feedback li { 
            margin-bottom: 0.25rem; 
            transition: color 0.3s;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }
        #password-feedback li.valid { 
            color: var(--success-color);
            background: rgba(16, 185, 129, 0.1);
        }
        #password-feedback li.invalid { 
            color: var(--danger-color);
            background: rgba(239, 68, 68, 0.1);
        }
        #password-feedback li.valid::before { content: '✓ '; font-weight: bold; }
        #password-feedback li.invalid::before { content: '✗ '; font-weight: bold; }

        #confirm-password-feedback {
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        #cep-feedback {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .progress-indicator {
            background: #e2e8f0;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
            margin: 2rem 0;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: 24px 24px 0 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card-register {
                border-radius: 16px;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo.jpg" alt="Logo MyHealth" width="140" height="70" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                            <a class="nav-link" href="contato.php">
                                <i class="fas fa-envelope me-2"></i>Contato
                            </a>
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
                    <div class="card-header-custom">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle p-3 mb-3">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                        <h1 class="h3 fw-bold">Crie sua Conta</h1>
                        <p class="mb-0 opacity-90">É rápido e fácil. Vamos começar sua jornada de saúde.</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="progress-indicator">
                            <div class="progress-bar" style="width: 0%" id="formProgress"></div>
                        </div>

                        <?php if ($notificacao): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $seguranca->sanitizar_entrada($notificacao['mensagem']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form id="formCadastro" action="Valida_cadastro.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $seguranca->gerar_csrf_token(); ?>">

                            <!-- Seção: Informações Básicas -->
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informações Básicas
                                </h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="tipo_usuario" class="form-label">
                                            <i class="fas fa-user-tag me-2"></i>Você é Médico ou Paciente?
                                        </label>
                                        <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                            <option value="paciente">Paciente</option>
                                            <option value="medico">Médico</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="identificador" class="form-label" id="label-identificador">
                                            <i class="fas fa-id-card me-2"></i>CPF
                                        </label>
                                        <input type="text" id="identificador" name="identificador" class="form-control" required placeholder="Digite seu CPF">
                                    </div>
                                    <div class="col-12">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-signature me-2"></i>Nome Completo
                                        </label>
                                        <input type="text" id="name" name="name" class="form-control" required placeholder="Digite seu nome completo">
                                    </div>
                                    <div class="col-12">
                                        <label for="nome_mae" class="form-label">
                                            <i class="fas fa-heart me-2"></i>Nome da Mãe
                                        </label>
                                        <input type="text" id="nome_mae" name="nome_mae" class="form-control" required placeholder="Digite o nome da sua mãe">
                                    </div>
                                </div>
                            </div>

                            <hr class="section-divider">

                            <!-- Seção: Informações Pessoais -->
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informações Pessoais
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nascimento" class="form-label">
                                            <i class="fas fa-birthday-cake me-2"></i>Data de Nascimento
                                        </label>
                                        <input type="date" id="nascimento" name="nascimento" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="genero" class="form-label">
                                            <i class="fas fa-venus-mars me-2"></i>Gênero
                                        </label>
                                        <select id="genero" name="genero" class="form-select" required>
                                            <option value="" disabled selected>Selecione o gênero</option>
                                            <option value="masculino">Masculino</option>
                                            <option value="feminino">Feminino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="convenio" class="form-label">
                                            <i class="fas fa-shield-alt me-2"></i>Convênio
                                        </label>
                                        <input type="text" id="convenio" name="convenio" class="form-control" required placeholder="Ex: Unimed, SUS">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="profissão" class="form-label">
                                            <i class="fas fa-briefcase me-2"></i>Profissão
                                        </label>
                                        <input type="text" id="profissão" name="profissão" class="form-control" required placeholder="Sua profissão">
                                    </div>
                                </div>
                            </div>

                            <hr class="section-divider">

                            <!-- Seção: Acesso -->
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-key me-2"></i>Informações de Acesso
                                </h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>E-mail
                                        </label>
                                        <input type="email" id="email" name="email" class="form-control" required placeholder="exemplo@email.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="senha" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Senha
                                        </label>
                                        <input type="password" id="senha" name="senha" class="form-control" required placeholder="Crie uma senha segura">
                                        <div id="password-feedback" class="mt-2"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirmar_senha" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Confirme sua Senha
                                        </label>
                                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required placeholder="Confirme sua senha">
                                        <div id="confirm-password-feedback" class="form-text mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="section-divider">

                            <!-- Seção: Contato e Endereço -->
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Contato e Endereço
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="telefone" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Telefone
                                        </label>
                                        <input type="tel" id="telefone" name="telefone" class="form-control" required placeholder="(00) 00000-0000">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cep" class="form-label">
                                            <i class="fas fa-map-pin me-2"></i>CEP
                                        </label>
                                        <input type="text" id="cep" name="cep" class="form-control" required placeholder="00000-000">
                                        <div id="cep-feedback" class="form-text"></div>
                                    </div>
                                    <div class="col-12">
                                        <label for="rua" class="form-label">
                                            <i class="fas fa-road me-2"></i>Rua
                                        </label>
                                        <input type="text" id="rua" name="rua" class="form-control" required readonly placeholder="Será preenchido automaticamente">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="numero" class="form-label">
                                            <i class="fas fa-hashtag me-2"></i>Número
                                        </label>
                                        <input type="text" id="numero" name="numero" class="form-control" required placeholder="123">
                                    </div>
                                    <div class="col-md-8">
                                        <label for="complemento" class="form-label">
                                            <i class="fas fa-plus me-2"></i>Complemento (Opcional)
                                        </label>
                                        <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apt, bloco, etc.">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btnCadastrar" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Finalizar Cadastro
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Já tem uma conta? 
                                <a href="login.php" class="text-primary fw-bold text-decoration-none">Faça login</a>
                            </p>
                        </div>
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
            const formProgress = document.getElementById('formProgress');
            const form = document.getElementById('formCadastro');
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
                labelIdentificador.innerHTML = (tipo === 'medico') ? '<i class="fas fa-id-card me-2"></i>CRM' : '<i class="fas fa-id-card me-2"></i>CPF';

                // Remove o campo UF se já existir (para evitar duplicação)
                const ufExistente = document.getElementById('uf');
                if (ufExistente) {
                    ufExistente.parentNode.removeChild(ufExistente);
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

            // --- MÁSCARA PARA TELEFONE ---
            const telefoneMask = IMask(document.getElementById('telefone'), {
                mask: '(00) 00000-0000'
            });

            // --- MÁSCARA PARA CEP ---
            const cepMask = IMask(document.getElementById('cep'), {
                mask: '00000-000'
            });

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
                updateProgress();
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
                    confirmPasswordFeedbackDiv.innerHTML = '<i class="fas fa-check me-1"></i>Senhas coincidem!';
                    confirmPasswordFeedbackDiv.className = 'form-text text-success fw-bold';
                } else {
                    confirmPasswordFeedbackDiv.innerHTML = '<i class="fas fa-times me-1"></i>As senhas não conferem.';
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
                
                cepFeedback.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Buscando...';
                cepFeedback.classList.remove('text-danger', 'text-success');
                cepFeedback.classList.add('text-primary');
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            cepFeedback.innerHTML = '<i class="fas fa-times me-1"></i>CEP não encontrado.';
                            cepFeedback.classList.remove('text-primary');
                            cepFeedback.classList.add('text-danger');
                            document.getElementById('rua').value = '';
                        } else {
                            cepFeedback.innerHTML = '<i class="fas fa-check me-1"></i>Endereço encontrado!';
                            cepFeedback.classList.remove('text-primary');
                            cepFeedback.classList.add('text-success');
                            document.getElementById('rua').value = data.logradouro;
                            document.getElementById('numero').focus();
                        }
                        updateProgress();
                    })
                    .catch(error => {
                        console.error("Erro ao buscar o CEP:", error);
                        cepFeedback.innerHTML = '<i class="fas fa-times me-1"></i>Erro ao buscar CEP.';
                        cepFeedback.classList.remove('text-primary');
                        cepFeedback.classList.add('text-danger');
                    });
            });

            // --- BARRA DE PROGRESSO ---
            function updateProgress() {
                const inputs = form.querySelectorAll('input[required], select[required]');
                let filled = 0;
                
                inputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        filled++;
                    }
                });
                
                const progress = (filled / inputs.length) * 100;
                formProgress.style.width = progress + '%';
            }

            // Adiciona listeners para todos os campos obrigatórios
            const requiredInputs = form.querySelectorAll('input[required], select[required]');
            requiredInputs.forEach(input => {
                input.addEventListener('input', updateProgress);
                input.addEventListener('change', updateProgress);
            });

            // --- ANIMAÇÃO DE ENTRADA ---
            const card = document.querySelector('.card-register');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);

            // Inicializa o progresso
            updateProgress();
        });
    </script>
</body>
</html>