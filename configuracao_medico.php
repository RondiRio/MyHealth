<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function avisoCRM(){
            return alert("Verifique seu CRM, essa inserção não pode ser alterada!")
        }

        
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cadastro de Médico</h2>
        <form action="Valida_Dados_medicos.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" >
            </div>
            <div class="mb-3">
                <label for="especialidade" class="form-label">Especialidade</label>
                <input type="text" class="form-control" id="especialidade" name="especialidade" >
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" >
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" >
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" >
            </div>
            <div class="mb-3">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" class="form-control" id="cidade" name="cidade" >
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <input type="text" class="form-control" id="estado" name="estado" >
            </div>
            <div class="mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" >
            </div>
            <div class="mb-3">
                <label class="form-label">Sexo</label>
                <select class="form-select" name="genero" >
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="instituicao_formacao" class="form-label">Instituição de Formação</label>
                <input type="text" class="form-control" id="instituicao_formacao" name="instituicao_formacao" >
            </div>
            <div class="mb-3">
                <label for="ano_formacao" class="form-label">Ano de Formação</label>
                <input type="number" class="form-control" id="ano_formacao" name="ano_formacao" min="1900" max="2025" >
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" >
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
            </div>
            <button type="submit" class="btn btn-primary w-100">Cadastrar Médico</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
