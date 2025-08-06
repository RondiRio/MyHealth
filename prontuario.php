<?php
require_once 'iniciar.php';
$seguranca->proteger_pagina('medico');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário Eletrônico do Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .prontuario-header {
            background: linear-gradient(to right, #0d6efd, #0d6efd);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
        }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #0d6efd; border-color: #dee2e6 #dee2e6 #fff; }
        .document-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    </style>
</head>
<body>
    <div class="container my-5">
        <!-- ETAPA 1: BUSCA DO PACIENTE -->
        <div id="busca-container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-5 fw-bold">Prontuário Eletrônico</h1>
                    <p class="lead mb-4">Digite o CPF do paciente para carregar o histórico completo.</p>
                    <form id="formBuscaProntuario">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="cpf_busca" class="form-control" placeholder="000.000.000-00" required>
                            <button class="btn btn-primary" type="submit">Buscar Paciente</button>
                        </div>
                    </form>
                    <div id="busca-feedback" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- ETAPA 2: EXIBIÇÃO DO PRONTUÁRIO (inicialmente oculto) -->
        <div id="prontuario-container" class="d-none">
            <div class="prontuario-header mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 id="paciente-nome" class="mb-0"></h2>
                        <span id="paciente-info" class="fs-5"></span>
                    </div>
                    <div>
                        <a href="consulta.php" class="btn btn-light"><i class="fas fa-plus me-2"></i> Nova Consulta</a>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="prontuarioTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#consultas">Consultas</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentos">Documentos</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#dados-pessoais">Dados Pessoais</button></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="consultas"><div class="accordion" id="accordionConsultas"></div></div>
                <div class="tab-pane fade" id="documentos"><div id="lista-documentos" class="row g-3"></div></div>
                <div class="tab-pane fade" id="dados-pessoais"><div class="card card-body"><div id="dados-paciente-detalhes"></div></div></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            IMask(document.getElementById('cpf_busca'), { mask: '000.000.000-00' });

            const formBusca = document.getElementById('formBuscaProntuario');
            const buscaFeedback = document.getElementById('busca-feedback');
            const buscaContainer = document.getElementById('busca-container');
            const prontuarioContainer = document.getElementById('prontuario-container');

            formBusca.addEventListener('submit', function(e) {
                e.preventDefault();
                const cpf = document.getElementById('cpf_busca').value;
                const submitBtn = formBusca.querySelector('button');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
                buscaFeedback.innerHTML = '';

                fetch('buscar_prontuario.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({cpf: cpf})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        preencherProntuario(data);
                        buscaContainer.classList.add('d-none');
                        prontuarioContainer.classList.remove('d-none');
                    } else {
                        buscaFeedback.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    buscaFeedback.innerHTML = `<div class="alert alert-danger">Ocorreu um erro ao buscar o prontuário.</div>`;
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Buscar Paciente';
                });
            });

            function preencherProntuario(data) {
                const escapeHTML = (str) => !str ? '' : str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
                
                // Cabeçalho
                document.getElementById('paciente-nome').textContent = escapeHTML(data.paciente.nome);
                let infoExtra = '';
                if (data.paciente.data_nascimento) {
                    const idade = new Date().getFullYear() - new Date(data.paciente.data_nascimento).getFullYear();
                    infoExtra += `${idade} anos`;
                }
                document.getElementById('paciente-info').textContent = infoExtra;

                // Aba de Consultas
                const accordionConsultas = document.getElementById('accordionConsultas');
                accordionConsultas.innerHTML = data.consultas.length > 0 ? data.consultas.map((c, i) => `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${i > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#c-${c.id_consulta}">
                                ${new Date(c.data_consulta).toLocaleDateString('pt-BR')} - ${escapeHTML(c.diagnostico_final) || 'Consulta de rotina'}
                            </button>
                        </h2>
                        <div id="c-${c.id_consulta}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}">
                            <div class="card-body"><strong>Médico:</strong> Dr. ${escapeHTML(c.nome_medico)}<br><strong>Anamnese:</strong> ${escapeHTML(c.anamnese) || 'N/A'}</div>
                        </div>
                    </div>
                `).join('') : '<p class="text-muted">Nenhum registro de consulta encontrado.</p>';

                // Aba de Documentos
                const listaDocumentos = document.getElementById('lista-documentos');
                listaDocumentos.innerHTML = data.documentos.length > 0 ? data.documentos.map(d => `
                    <div class="col-md-4">
                        <a href="uploads/documentos_pacientes/${escapeHTML(d.nome_arquivo)}" target="_blank" class="card document-card text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-3x text-primary mb-2"></i>
                                <h6 class="card-title mb-0">${escapeHTML(d.titulo_documento)}</h6>
                            </div>
                        </a>
                    </div>
                `).join('') : '<p class="text-muted">Nenhum documento anexado.</p>';
                
                // Aba de Dados Pessoais
                const detalhesPaciente = document.getElementById('dados-paciente-detalhes');
                detalhesPaciente.innerHTML = `<p><strong>CPF:</strong> ${escapeHTML(data.paciente.cpf)}</p><p><strong>RG:</strong> ${escapeHTML(data.paciente.rg) || 'N/A'}</p>`;
            }
        });
    </script>
</body>
</html>
