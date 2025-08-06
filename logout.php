<?php 

// fim da sessao do usuário
session_unset();
session_destroy();

header('Location: index.php'); // Redireciona para a página de login
?>