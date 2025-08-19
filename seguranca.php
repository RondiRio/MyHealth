<?php


// Versão original do código de configuração de segurança
// V1
// class Seguranca {
    
//     public function __construct() {
//         $this->configurarSessao();
//         $this->iniciarSessao();
//         $this->enviarCabecalhosDeSeguranca();
//     }

//     /**
//      * Configura todas as diretivas de segurança da sessão.
//      */
//     private function configurarSessao() {
//         ini_set('session.cookie_httponly', 1);
//         ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));
//         ini_set('session.use_strict_mode', 1);
//         ini_set('session.use_only_cookies', 1);

//         session_name('MyHealthSession');
//         session_set_cookie_params([
//             'lifetime' => 0, 'path' => '/',
//             'domain' => $_SERVER['HTTP_HOST'],
//             'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
//             'httponly' => true, 'samesite' => 'Strict'
//         ]);
//     }
    
//     /**
//      * Inicia a sessão se ela ainda não estiver ativa.
//      */
//     private function iniciarSessao() {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
//     }

//     /**
//      * Envia todos os cabeçalhos de segurança HTTP.
//      */
//     // DENTRO DO ARQUIVO Seguranca.php

// private function enviarCabecalhosDeSeguranca() {
//     header('X-Frame-Options: DENY');
//     header('X-Content-Type-Options: nosniff');
//     header('Referrer-Policy: no-referrer');
    
//     if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
//         header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
//     }
//     // Adicionamos as permissões para os CDNs do Bootstrap e Font Awesome
//     header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src 'self' https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com;");

//     header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
// }

//     /**
//      * Protege página restrita (combina todas as suas validações).
//      */
//     public function proteger_pagina($tipoUsuarioPermitido = null) {
//         // Validação de método HTTP
//         if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
//             $this->registrarLogAcesso('Método HTTP não permitido: ' . $_SERVER['REQUEST_METHOD']);
//             http_response_code(405); exit;
//         }

//         // Validação de autenticação
//         if (empty($_SESSION['usuario_id'])) {
//             $this->destruirSessaoEAbortar('Acesso negado: usuário não autenticado', 'login.php?erro=2');
//         }
        
//         // NOVO: Validação de tipo de usuário
//         if ($tipoUsuarioPermitido !== null) {
//             if (empty($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== $tipoUsuarioPermitido) {
//                 $this->destruirSessaoEAbortar('Acesso negado: tipo de usuário não permitido', 'naoacessou.php');
//             }
//         }

//         // Validação de User Agent e IP
//         $this->validarIdentidadeSessao();
        
//         // Validação de Timeout
//         $this->validarTimeoutSessao();

//         // Se passou por tudo, registra acesso autorizado e atualiza a atividade
//         $_SESSION['last_activity'] = time();
//         $this->registrarLogAcesso('Acesso autorizado para ' . $_SESSION['usuario_id']);
//     }
    
//     // As funções abaixo são as mesmas que você criou, agora como métodos da classe.
    
//     public function sanitizar_entrada($entrada) {
//         return is_array($entrada) ? array_map([$this, 'sanitizar_entrada'], $entrada) : htmlspecialchars(strip_tags(trim($entrada)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
//     }

//     public function validar_entrada($entrada, $maxLength = 255) {
//         $entrada = $this->sanitizar_entrada($entrada);
//         return mb_substr($entrada, 0, $maxLength, 'UTF-8');
//     }

//     public function gerar_csrf_token() {
//         if (empty($_SESSION['csrf_token'])) {
//             $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//         }
//         return $_SESSION['csrf_token'];
//     }

//     public function validar_csrf_token($token) {
//         return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
//     }

//     // Métodos privados de ajuda para manter o código limpo
//     private function registrarLogAcesso($mensagem) {
//         $arquivo = __DIR__ . '/logs_acesso.txt';
//         $linha = sprintf("[%s] IP: %s | UA: %s | %s%s",
//             date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'] ?? 'IP desc.',
//             $_SERVER['HTTP_USER_AGENT'] ?? 'UA desc.', $mensagem, PHP_EOL
//         );
//         file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX);
//     }
    
//     private function destruirSessaoEAbortar($logMensagem, $redirecionarPara) {
//         $this->registrarLogAcesso($logMensagem);
//         session_regenerate_id(true);
//         session_destroy();
//         header("Location: " . $redirecionarPara);
//         exit;
//     }

//     private function validarIdentidadeSessao() {
//         $userAgent = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
//         $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

//         if ($_SESSION['user_agent'] !== $userAgent) {
//             $this->destruirSessaoEAbortar('User-Agent alterado', 'login.php?erro=3');
//         }
//         if ($_SESSION['ip_address'] !== $ipAddress) {
//             $this->destruirSessaoEAbortar('IP alterado', 'login.php?erro=4');
//         }
//     }
    
//     private function validarTimeoutSessao() {
//         $timeout = 900; // 15 minutos
//         if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
//             $this->destruirSessaoEAbortar('Sessão expirada', 'login.php?erro=5');
//         }
//     }
// }



// Versão do Gemini 
// V2
// Versão simplificada do código de configuração do médico

/**
 * VERSÃO DE DESENVOLVIMENTO MINIMALISTA
 * A segurança foi "zerada" para focar na lógica da aplicação.
 * ATENÇÃO: É crucial reativar as camadas de segurança antes de ir para a produção.
 */
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