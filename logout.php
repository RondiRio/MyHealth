<?php
// 1. Carrega o nosso ambiente, que já inicia a sessão correta ('MyHealthSession')
require_once 'iniciar.php';

// 2. Limpa todas as variáveis da sessão
$_SESSION = array();

// 3. Destrói a sessão atual
session_destroy();

// 4. Redireciona o utilizador para a página inicial
header("Location: index.php");
exit;