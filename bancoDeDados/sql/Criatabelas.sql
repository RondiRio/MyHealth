-- Tabela de Médicos
CREATE TABLE user_medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    crm VARCHAR(20) UNIQUE NOT NULL,
    telefone VARCHAR(15),
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(100),
    data_nascimento DATE,
    genero ENUM('Masculino', 'Feminino', 'Outro'),
    especialidade VARCHAR(255),
    ano_formacao INT CHECK (ano_formacao >= 1900 AND ano_formacao <= YEAR(CURDATE())),
    status ENUM('Ativo', 'Inativo') DEFAULT 'Ativo',
    foto_perfil VARCHAR(255) DEFAULT 'default.jpg'
);

-- Tabela de Pacientes
CREATE TABLE user_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(15),
    data_nascimento DATE,
    sexo ENUM('Masculino', 'Feminino', 'Outro'),
    tipo_sanguineo ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    foto_perfil VARCHAR(255) DEFAULT 'default.jpg'
);

-- Tabela de Prontuários
CREATE TABLE prontuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    data_consulta DATE NOT NULL,
    diagnostico TEXT,
    observacoes TEXT,
    exames TEXT,
    tratamento TEXT,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES user_medicos(id) ON DELETE CASCADE
);

-- Tabela de Alergias
CREATE TABLE alergias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Medicamentos
CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    nome_medicamento VARCHAR(100) NOT NULL,
    dosagem VARCHAR(50),
    frequencia VARCHAR(100),
    inicio_tratamento DATE,
    fim_tratamento DATE,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Histórico de Doenças
CREATE TABLE historico_doencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    doenca VARCHAR(255) NOT NULL,
    data_diagnostico DATE NOT NULL,
    status_doenca ENUM('Em tratamento', 'Curado', 'Em observação', 'Grave') NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Vacinas
CREATE TABLE vacinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    nome_vacina VARCHAR(100) NOT NULL,
    data_aplicacao DATE NOT NULL,
    proxima_vacina DATE,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Histórico de Visitas
CREATE TABLE historico_visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    hospital_id INT NOT NULL,
    data_visita DATE NOT NULL,
    motivo_visita TEXT,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitais(id) ON DELETE CASCADE
);

-- Tabela de Marcapasso
CREATE TABLE marcapasso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    data_implante DATE NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Hospitais
CREATE TABLE hospitais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    endereco TEXT NOT NULL,
    cidade VARCHAR(100),
    estado VARCHAR(100)
);

-- Tabela de Consultas de Pacientes
CREATE TABLE consultas_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    data_consulta DATE NOT NULL,
    hospital_id INT NOT NULL,
    diagnostico TEXT,
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES user_medicos(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitais(id) ON DELETE CASCADE
);

-- Tabela de Endereços (Evita repetição de dados nos médicos e pacientes)
CREATE TABLE enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_usuario ENUM('medico', 'paciente') NOT NULL,
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(10),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(100),
    cep VARCHAR(10),
    FOREIGN KEY (usuario_id) REFERENCES user_pacientes(id) ON DELETE CASCADE
);

-- Tabela de Agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    data_agendamento DATETIME NOT NULL,
    status ENUM('Pendente', 'Confirmado', 'Cancelado', 'Realizado') DEFAULT 'Pendente',
    FOREIGN KEY (paciente_id) REFERENCES user_pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES user_medicos(id) ON DELETE CASCADE
);
