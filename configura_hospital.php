<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Hospitais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cadastro de Hospitais</h2>
        <form action="Valida_Dados_hospital.php" method="POST">
    <h4>Vincular a um hospital existente</h4>
    <select name="hospital_id[]" class="form-select" multiple required>
        <?php
        include('bancoDeDados/sql/conexaoBD.php');
        $conexao = new ConexaoBD();
        $conn = $conexao->getConexao();

        $sql = "SELECT id, nome FROM hospitais";
        $result = $conn->query($sql);

        while ($hospital = $result->fetch_assoc()) {
            echo "<option value='{$hospital['id']}'>{$hospital['nome']}</option>";
        }
        ?>
    </select>
    <small>Segure `Ctrl` (Windows) ou `Command` (Mac) para selecionar múltiplos hospitais.</small>

    <h4>Ou cadastrar um novo hospital</h4>
    <label for="nome">Nome do Hospital</label>
    <input type="text" class="form-control" name="nome">

    <label for="cnpj">CNPJ</label>
    <input type="text" class="form-control" name="cnpj">

    <label for="telefone">Telefone</label>
    <input type="text" class="form-control" name="telefone">

    <label for="endereco">Endereço</label>
    <input type="text" class="form-control" name="endereco">

    <label for="cidade">Cidade</label>
    <input type="text" class="form-control" name="cidade">

    <label for="estado">Estado</label>
    <input type="text" class="form-control" name="estado">

    <button type="submit" class="btn btn-primary mt-3">Cadastrar/Vincular</button>
</form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
