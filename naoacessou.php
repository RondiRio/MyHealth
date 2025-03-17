<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro de Atendimento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-size: 24px;
            color: #343a40;
        }
        p {
            font-size: 18px;
            color: #6c757d;
        }
        .btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Não conseguimos te atender no momento.</h1>
        <p>Que tal tentar mais tarde ou rever a sua senha?</p>
        <a href="index.php" class="btn btn-primary">Tentar novamente</a>
        <a href="recuperar_senha.php" class="btn btn-secondary">Rever senha</a>
    </div>
</body>
</html>
