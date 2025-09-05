<?php
class Seguranca {
    
    public function __construct() {
        // A única ação no construtor é iniciar a sessão.
        $this->iniciarSessao();
    }
    
    private function iniciarSessao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * VERSÃO MÍNIMA DA PROTEÇÃO DE PÁGINA
     * Verifica APENAS se o usuário está logado (se $_SESSION['usuario_id'] existe).
     * Todas as outras verificações foram removidas.
     */
    public function proteger_pagina($tipoUsuarioPermitido = null) {
        if (empty($_SESSION['usuario_id'])) {
            header("Location: index.php?erro=sessa_expirada");
            exit;
        }
        
        // A verificação de tipo de usuário foi mantida por ser parte da lógica do app.
        if ($tipoUsuarioPermitido && (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== $tipoUsuarioPermitido)) {
             header("Location: naoacessou.php");
             exit;
        }
    }
    
    // Os métodos úteis de sanitização e CSRF foram mantidos,
    // pois não interferem no fluxo da sessão.
    
    public function sanitizar_entrada($entrada, $maxLength = 255) {
    // Se a entrada for um array, chama a função para cada item e retorna o array limpo.
    if (is_array($entrada)) {
        return array_map([$this, 'sanitizar_entrada'], $entrada);
    }

    // Se não for um array, sabemos que é um texto.
    // Primeiro, limpamos o texto.
    $stringSanitizada = htmlspecialchars(strip_tags(trim($entrada)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Depois, aplicamos o limite de tamanho apenas no texto já limpo.
    return mb_substr($stringSanitizada, 0, $maxLength, 'UTF-8');
}

    public function gerar_csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validar_csrf_token($token) {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}