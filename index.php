<?php
$host = 'localhost';
$usuario = 'root';
$senha = ''; 
$banco = 'myhealth';

mysqli_report(MYSQLI_REPORT_OFF);

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    http_response_code(500);
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Erro 500 - Erro Interno</title>
        <style>
            body { background-color: #f2f2f2; font-family: Arial, sans-serif; text-align: center; padding-top: 50px; }
            .container { background-color: #fff; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; }
            h1 { color: #d9534f; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Erro 500 - Erro Interno do Servidor</h1>
            <p>Não foi possível estabelecer uma conexão com o serviço principal.</p>
            <p>Por favor, tente novamente mais tarde. Se o problema persistir, contate o suporte.</p>
        </div>
    </body>
    </html>
    HTML;
    exit();
}
elseif(file_exists('maintenance.txt')){
    http_response_code(503);
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Em Manutenção</title>
        <style>
            body { background-color: #411a1aff; font-family: Arial, sans-serif; text-align: center; padding-top: 50px; }
            .container { background-color: #fff; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; }
            h1 { color: #f0ad4e; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Site em Manutenção</h1>
            <p>Estamos realizando uma manutenção programada.</p>
            <p>Por favor, tente novamente mais tarde.</p>
        </div>
    </body>
    </html>
    HTML;
    exit();
}
else{
    header('Location: publics/');
    exit();
}


?>