<?php
declare(strict_types=1);

class Paciente {
    private mysqli $conn;
    private int $paciente_id;

    /**
     * O construtor recebe a conexão com o BD e o ID do paciente logado.
     * @param mysqli $db_connection Conexão ativa com o banco de dados.
     * @param int $id O ID do paciente logado na sessão.
     */
    public function __construct(mysqli $db_connection, int $id) {
        $this->conn = $db_connection;
        $this->paciente_id = $id;
    }

    //======================================================================
    // MÉTODOS DE LEITURA (SELECT) - Para popular o dashboard
    //======================================================================

    /**
     * Busca as informações de perfil do paciente.
     * @return array|null Retorna os dados do perfil ou null se não encontrar.
     */
    public function getProfileInfo(): ?array {
        $stmt = $this->conn->prepare("SELECT id, nome_paciente, foto_perfil, data_nascimento FROM user_pacientes WHERE id = ?");
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Busca todas as alergias do paciente, separadas por tipo.
     * @return array Retorna um array com as alergias.
     */
    public function getAllergies(): array {
        $stmt = $this->conn->prepare("SELECT id, tipo, nome_agente, sintomas FROM alergias WHERE id_paciente = ? ORDER BY tipo, nome_agente");
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $alergias = ['alimentares' => [], 'respiratorias' => []];
        while ($row = $result->fetch_assoc()) {
            $alergias[$row['tipo']][] = $row;
        }
        return $alergias;
    }

    /**
     * Busca os últimos registros dos principais sinais vitais.
     * @return array Retorna um array com os últimos registros.
     */
    public function getLatestVitalSigns(): array {
        $tipos = ['Pressão Arterial', 'Glicemia', 'Peso'];
        $latestVitals = [];
        foreach ($tipos as $tipo) {
            $stmt = $this->conn->prepare(
                "SELECT valor1, valor2, unidade, DATE_FORMAT(data_registro, '%d/%m/%Y %H:%i') as data_formatada 
                 FROM sinais_vitais WHERE id_paciente = ? AND tipo = ? 
                 ORDER BY data_registro DESC LIMIT 1"
            );
            $stmt->bind_param("is", $this->paciente_id, $tipo);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result) {
                $latestVitals[$tipo] = $result;
            }
        }
        return $latestVitals;
    }

    /**
     * Busca os medicamentos prescritos para o paciente para o dia de hoje.
     * @return array Retorna a lista de medicamentos.
     */
    public function getMedicationsForToday(): array {
        $query = "
            SELECT 
                mp.id, 
                mp.nome_medicamento, 
                mp.dosagem, 
                DATE_FORMAT(mp.horario_programado, '%H:%i') as horario,
                (SELECT COUNT(*) FROM medicamentos_log ml WHERE ml.id_prescricao = mp.id AND DATE(ml.data_hora_tomado) = CURDATE()) as tomado
            FROM medicamentos_prescritos mp
            WHERE mp.id_paciente = ? 
              AND CURDATE() >= mp.data_inicio 
              AND (mp.data_fim IS NULL OR CURDATE() <= mp.data_fim)
            ORDER BY mp.horario_programado;
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Busca as metas de saúde ativas para o paciente.
     * @return array Retorna a lista de metas.
     */
    public function getHealthGoals(): array {
        $query = "
            SELECT 
                m.id,
                m.descricao_meta,
                (SELECT COUNT(*) FROM metas_saude_log ml WHERE ml.id_meta = m.id AND ml.data_conclusao = CURDATE()) as concluida
            FROM metas_saude m
            WHERE m.id_paciente = ? AND m.status = 'ativa';
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Busca e unifica todos os eventos para montar a linha do tempo.
     * @return array Retorna a lista de eventos ordenada por data.
     */
    public function getHealthTimeline(): array {
        $query = "
            (SELECT 
                data_registro as event_date, 
                'sintoma' as event_type, 
                CONCAT('Registro de - ', tipo) as title, 
                CONCAT('Valor: ', valor1, IFNULL(CONCAT('/', valor2), ''), ' ', unidade, '. ', observacoes) as description,
                'fas fa-thermometer-half' as icon
            FROM sinais_vitais 
            WHERE id_paciente = ?)
            UNION ALL
            (SELECT 
                data_upload as event_date, 
                'exame' as event_type, 
                CONCAT('Exame Adicionado - ', nome_documento) as title, 
                CONCAT(observacoes, ' <a href=\"', caminho_arquivo, '\" target=\"_blank\" class=\"text-primary fw-bold\">Visualizar documento</a>') as description,
                'fas fa-file-medical' as icon
            FROM documentos_paciente 
            WHERE id_paciente = ?)
            -- Futuramente, adicione UNION ALL para a tabela de 'consultas' aqui
            ORDER BY event_date DESC
            LIMIT 20;
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->paciente_id, $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //======================================================================
    // MÉTODOS DE ESCRITA (INSERT, DELETE) - Ações do usuário
    //======================================================================

    /**
     * Adiciona uma nova alergia para o paciente.
     * @return bool Retorna true em caso de sucesso, false em caso de erro.
     */
    public function addAllergy(string $tipo, string $nomeAgente, string $sintomas): bool {
        $stmt = $this->conn->prepare("INSERT INTO alergias (id_paciente, tipo, nome_agente, sintomas) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $this->paciente_id, $tipo, $nomeAgente, $sintomas);
        return $stmt->execute();
    }

    /**
     * Deleta uma alergia do paciente.
     * @param int $allergyId O ID da alergia a ser deletada.
     * @return bool Retorna true se a deleção for bem-sucedida.
     */
    public function deleteAllergy(int $allergyId): bool {
        $stmt = $this->conn->prepare("DELETE FROM alergias WHERE id = ? AND id_paciente = ?");
        $stmt->bind_param("ii", $allergyId, $this->paciente_id);
        return $stmt->execute();
    }
    
    /**
     * Adiciona um novo registro de sinal vital.
     * @return bool Retorna true em caso de sucesso.
     */
    public function addVitalSign(string $tipo, string $valor1, ?string $valor2, string $unidade, string $observacoes): bool {
        $stmt = $this->conn->prepare(
            "INSERT INTO sinais_vitais (id_paciente, tipo, valor1, valor2, unidade, observacoes, data_registro) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("isssss", $this->paciente_id, $tipo, $valor1, $valor2, $unidade, $observacoes);
        return $stmt->execute();
    }
    
    /**
     * Processa o upload de um arquivo de exame e salva o registro no banco.
     * @param array $file O array $_FILES['arquivo'] vindo do formulário.
     * @return bool Retorna true em caso de sucesso.
     */
    public function uploadExam(string $nomeDocumento, string $dataExame, string $observacoes, array $file): bool {
        $uploadDir = 'uploads/exames/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeFileName = uniqid('exame_' . $this->paciente_id . '_', true) . '.' . $fileExtension;
        $filePath = $uploadDir . $safeFileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $this->conn->prepare(
                "INSERT INTO documentos_paciente (id_paciente, nome_documento, caminho_arquivo, data_upload, observacoes) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issss", $this->paciente_id, $nomeDocumento, $filePath, $dataExame, $observacoes);
            return $stmt->execute();
        }
        
        return false;
    
    
    }

    public function getConsultasVisiveis(): array {
        $sql = "SELECT c.*, m.nome as nome_medico
                FROM consultas c
                JOIN user_medicos m ON c.id_medico = m.id
                WHERE c.paciente_id = ? AND c.visivel_para_paciente = TRUE
                ORDER BY c.data_consulta DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Conta quantas consultas o paciente teve nos últimos 30 dias.
     * @return int O número de consultas recentes.
     */
    public function getContagemConsultasRecentes(): int {
        $sql = "SELECT COUNT(id_consulta) as total 
                FROM consultas 
                WHERE paciente_id = ? 
                  AND visivel_para_paciente = TRUE 
                  AND data_consulta >= CURDATE() - INTERVAL 30 DAY";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->paciente_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)($result['total'] ?? 0);
    }
}
?>