CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    data_nascimento DATE,
    sexo ENUM('M', 'F', 'Outro') NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(15),
    endereco TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE prontuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    medico_id INT,
    data_consulta DATE NOT NULL,
    diagnostico TEXT,
    observacoes TEXT,
    exames TEXT,
    tratamento TEXT,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE
);
CREATE TABLE alergias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    descricao VARCHAR(255),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    nome_medicamento VARCHAR(100),
    dosagem VARCHAR(50),
    frequencia VARCHAR(100),
    inicio_tratamento DATE,
    fim_tratamento DATE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE historico_doencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    doenca VARCHAR(255),
    data_diagnostico DATE,
    status_doenca ENUM('Em tratamento', 'Curado', 'Em observação', 'Grave') NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE vacinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    nome_vacina VARCHAR(100),
    data_aplicacao DATE,
    proxima_vacina DATE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE historico_visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    data_visita DATE,
    motivo_visita TEXT,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE marcapasso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    tipo VARCHAR(100),
    data_implante DATE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);
CREATE TABLE user_medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    crm VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE user_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL
);

