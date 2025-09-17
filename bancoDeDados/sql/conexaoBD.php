<?php
class ConexaoBD {
    private $servername = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'myhealth';
    
    // MUDANÇA 1: De public para private para melhor encapsulamento.
    private $conn;

    public function __construct() {
        $this->conectar();
    }

    private function conectar() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

        // MUDANÇA 2: Tratamento de erro mais seguro.
        // Registra o erro detalhado para o desenvolvedor, mas mostra uma mensagem genérica para o usuário.
        if ($this->conn->connect_error) {
            error_log('Falha na conexão com o Banco de Dados: ' . $this->conn->connect_error);
            die('Erro interno do servidor. Por favor, tente novamente mais tarde.');
        }
        
        // Boa prática para garantir a codificação correta.
        $this->conn->set_charset('utf8mb4');
    }

    public function getConexao() {
        return $this->conn;
    }

    public function proteger_sql($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            $error_SQL = error_log('Erro na preparação da query SQL: ' . $this->conn->error);
            throw new mysqli_sql_exception($error_SQL);
            // die('Erro interno do servidor.');
        }

        if ($params) {
            $types = str_repeat('s', count($params)); // Trata todos como string para simplificar
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $error_SQL = error_log('Erro na execução da query SQL: ' . $stmt->error);
            throw new mysqli_sql_exception($error_SQL);
            // die('Erro interno do servidor.');
        }
        
        return $stmt;
    }
}
?>