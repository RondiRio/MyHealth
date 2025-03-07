<?php
    class ConexaoBD{
        private $servername = 'localhost';
        private $username = 'root';
        private $password = '';
        private $database = 'myhealth';
        public $conn;

        public function __construct(){
            $this->conectar();
        }

        private function conectar(){
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

            if($this->conn->connect_error){
                die('Falha ao conectar: '. $this->conn->connect_error);
            }
        }

        public function getConexao(){
            return $this->conn;
        }
    }
?>