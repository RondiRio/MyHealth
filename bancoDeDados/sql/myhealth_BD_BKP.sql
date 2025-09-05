-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/08/2025 às 13:46
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Banco de dados: `myhealth`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `alergias`
--

DROP TABLE IF EXISTS `alergias`;
CREATE TABLE IF NOT EXISTS `alergias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `tipo` enum('alimentar', 'respiratoria') NOT NULL,
  `nome_agente` varchar(100) NOT NULL,
  `sintomas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `alergias`:
--   `id_paciente`
--       `user_pacientes` -> `id`
--

--
-- Despejando dados para a tabela `alergias`
--

INSERT INTO `alergias` (
    `id`,
    `id_paciente`,
    `tipo`,
    `nome_agente`,
    `sintomas`
  )
VALUES (1, 5, 'alimentar', 'Amendoim', 'Inchaço '),
  (2, 5, 'alimentar', 'Amendoim', 'Inchaço ');
-- --------------------------------------------------------
--
-- Estrutura para tabela `consultas`
--

DROP TABLE IF EXISTS `consultas`;
CREATE TABLE IF NOT EXISTS `consultas` (
  `id_consulta` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) DEFAULT NULL COMMENT 'ID do paciente, se ele tiver cadastro no sistema (opcional).',
  `id_medico` int(11) NOT NULL COMMENT 'ID do médico que realizou a consulta.',
  `id_hospital` int(11) DEFAULT NULL COMMENT 'ID do hospital onde a consulta foi realizada (opcional).',
  `cpf_paciente` varchar(14) NOT NULL,
  `nome_paciente` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `convenio` varchar(100) DEFAULT NULL,
  `tipo_consulta` varchar(100) DEFAULT NULL,
  `data_consulta` datetime NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `status_consulta` enum('realizada', 'agendada', 'cancelada') DEFAULT 'realizada',
  `anamnese` text DEFAULT NULL COMMENT 'Queixas e histórico do paciente.',
  `exame_fisico` text DEFAULT NULL COMMENT 'Observações do exame físico.',
  `hipotese_diagnostica` text DEFAULT NULL COMMENT 'Suspeitas e diagnósticos diferenciais.',
  `diagnostico_final` text DEFAULT NULL COMMENT 'Diagnóstico confirmado.',
  `cid_10` varchar(10) DEFAULT NULL,
  `diagnosticos_secundarios` text DEFAULT NULL,
  `exames_solicitados` text DEFAULT NULL,
  `tratamento_proposto` text DEFAULT NULL COMMENT 'Prescrições, receitas e recomendações.',
  `orientacoes_paciente` text DEFAULT NULL,
  `encaminhamentos` text DEFAULT NULL,
  `data_retorno` date DEFAULT NULL,
  `prognostico` varchar(50) DEFAULT NULL,
  `observacoes_privadas` text DEFAULT NULL COMMENT 'Anotações internas do médico, não visíveis ao paciente.',
  `visivel_para_paciente` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se TRUE (1), o paciente pode ver este registro.',
  `consulta_urgencia` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_consulta`),
  KEY `paciente_id` (`paciente_id`),
  KEY `id_medico` (`id_medico`),
  KEY `id_hospital` (`id_hospital`)
) ENGINE = InnoDB AUTO_INCREMENT = 17 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `consultas`:
--   `id_medico`
--       `user_medicos` -> `id`
--   `paciente_id`
--       `user_pacientes` -> `id`
--   `paciente_id`
--       `user_pacientes` -> `id`
--

--
-- Despejando dados para a tabela `consultas`
--

INSERT INTO `consultas` (
    `id_consulta`,
    `paciente_id`,
    `id_medico`,
    `id_hospital`,
    `cpf_paciente`,
    `nome_paciente`,
    `data_nascimento`,
    `genero`,
    `convenio`,
    `tipo_consulta`,
    `data_consulta`,
    `especialidade`,
    `status_consulta`,
    `anamnese`,
    `exame_fisico`,
    `hipotese_diagnostica`,
    `diagnostico_final`,
    `cid_10`,
    `diagnosticos_secundarios`,
    `exames_solicitados`,
    `tratamento_proposto`,
    `orientacoes_paciente`,
    `encaminhamentos`,
    `data_retorno`,
    `prognostico`,
    `observacoes_privadas`,
    `visivel_para_paciente`,
    `consulta_urgencia`,
    `criado_em`
  )
VALUES (
    14,
    5,
    1,
    NULL,
    '169.974.247-24',
    'Rondineli Oliveira',
    '1996-07-12',
    'Masculino',
    'SUS',
    'Consulta de Rotina',
    '2025-08-25 19:23:00',
    NULL,
    'realizada',
    'QUEIXA PRINCIPAL E HDA:\nPaciente chegou ao hospital com dor de dente\n\nHISTÓRICO PATOLÓGICO PREGRESSO:\npaciente possui TDAH, possui queimaduras de 3º grau\n\nHISTÓRICO FAMILIAR:\nMãe Hipertensa\n\nALERGIAS E MEDICAMENTOS EM USO:\nAlergia a Amendoim\n\nHÁBITOS DE VIDA:\nSedentário\n\nREVISÃO DE SISTEMAS:\n',
    'ESTADO GERAL E FÁCIES:\nHidratado, cor típica\n\nCABEÇA E PESCOÇO:\nnormal\n\nAPARELHO CARDIOVASCULAR:\nnormal\n\nAPARELHO RESPIRATÓRIO:\nnormal\n\nABDOME:\nnormal\n\nEXTREMIDADES:\nnormal\n\nSISTEMA NEUROLÓGICO:\nnormal\n\nRESUMO / OUTROS ACHADOS:\nnormal',
    'Carie',
    'K02-Carie',
    'K02',
    NULL,
    NULL,
    'PRESCRIÇÃO / TRATAMENTO:\nTomar Dipirona de 12 em 12 horas\n\nORIENTAÇÕES AO PACIENTE:\nCuidado com a escovação. Escovar os dentes no mínimo 3x ao dia\n\nENCAMINHAMENTOS:\n',
    NULL,
    NULL,
    NULL,
    NULL,
    '',
    1,
    0,
    '2025-08-25 22:31:30'
  ),
  (
    15,
    5,
    1,
    NULL,
    '169.974.247-24',
    'Rondineli Oliveira',
    '1995-07-12',
    'Masculino',
    'SUS',
    'Consulta de Rotina',
    '2025-08-25 19:23:00',
    NULL,
    'realizada',
    NULL,
    NULL,
    'Paciente possivelmente tem carie no dente',
    'K02-Carie',
    'K02',
    'Paciente tem uma abertura nos dentes que deve ser tratada',
    'Fazer um Raio X panorâmico, e outro centralizado',
    'Tomar Dipirona de 12 em 12 horas',
    'Cuidado com a escovação. Escovar os dentes no mínimo 3x ao dia',
    '',
    NULL,
    'Reservado',
    '',
    1,
    1,
    '2025-08-25 22:34:15'
  ),
  (
    16,
    6,
    1,
    NULL,
    '356.867.667-20',
    'Antonio Couto Vitória',
    '1954-10-16',
    'Masculino',
    'Unimed - RJ',
    'Consulta de Rotina',
    '2025-08-25 19:34:00',
    NULL,
    'realizada',
    NULL,
    NULL,
    'açsldjçalsljd aslçdjçalsjd',
    'açsldjaçlsdj',
    'açslfd',
    'çasldjçlasdj',
    'çasldjçalsdj',
    'as.dkjasçlldjaç',
    'çjdçalsjdçalsjd',
    'çlasjdçlajsd',
    '2025-09-21',
    'Reservado',
    'çaskldhjksdhkj asldkjjalskjd asldkjjjkaçl alskdnnçaksjdçaljs aslkjdçkalsjdl',
    1,
    1,
    '2025-08-25 22:36:50'
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `consultas_anamnese`
--

DROP TABLE IF EXISTS `consultas_anamnese`;
CREATE TABLE IF NOT EXISTS `consultas_anamnese` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_consulta` int(11) NOT NULL,
  `queixa_principal_hda` text DEFAULT NULL,
  `historico_patologico` text DEFAULT NULL,
  `historico_familiar` text DEFAULT NULL,
  `alergias_medicamentos_uso` text DEFAULT NULL,
  `habitos_vida` text DEFAULT NULL,
  `revisao_sistemas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_consulta` (`id_consulta`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `consultas_anamnese`:
--   `id_consulta`
--       `consultas` -> `id_consulta`
--

--
-- Despejando dados para a tabela `consultas_anamnese`
--

INSERT INTO `consultas_anamnese` (
    `id`,
    `id_consulta`,
    `queixa_principal_hda`,
    `historico_patologico`,
    `historico_familiar`,
    `alergias_medicamentos_uso`,
    `habitos_vida`,
    `revisao_sistemas`
  )
VALUES (
    1,
    15,
    'Paciente chegou ao hospital com dor de dente',
    'paciente possui TDAH, possui queimaduras de 3º grau',
    'Mãe Hipertensa',
    'Alergia a Amendoim',
    'Sedentário',
    ''
  ),
  (
    2,
    16,
    'açslkjdçasj',
    'açslsljdçalsjdk',
    'çalsjdçlsjd',
    'çaledjsfçljda',
    'çalsdjçalsjd',
    'çqlsfdaçls'
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `consultas_exame_fisico`
--

DROP TABLE IF EXISTS `consultas_exame_fisico`;
CREATE TABLE IF NOT EXISTS `consultas_exame_fisico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_consulta` int(11) NOT NULL,
  `estado_geral` text DEFAULT NULL,
  `cabeca_pescoco` text DEFAULT NULL,
  `aparelho_cardiovascular` text DEFAULT NULL,
  `aparelho_respiratorio` text DEFAULT NULL,
  `abdome` text DEFAULT NULL,
  `extremidades` text DEFAULT NULL,
  `sistema_neurologico` text DEFAULT NULL,
  `resumo_exame_fisico` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_consulta` (`id_consulta`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `consultas_exame_fisico`:
--   `id_consulta`
--       `consultas` -> `id_consulta`
--

--
-- Despejando dados para a tabela `consultas_exame_fisico`
--

INSERT INTO `consultas_exame_fisico` (
    `id`,
    `id_consulta`,
    `estado_geral`,
    `cabeca_pescoco`,
    `aparelho_cardiovascular`,
    `aparelho_respiratorio`,
    `abdome`,
    `extremidades`,
    `sistema_neurologico`,
    `resumo_exame_fisico`
  )
VALUES (
    1,
    15,
    'Hidratado, cor típica',
    'normal',
    'normal',
    'normal',
    'normal',
    'normal',
    'normal',
    'normal'
  ),
  (
    2,
    16,
    'asldkasldkh',
    'laskdjlaskdj',
    'lçaskdjlakjd',
    'laskdlkasd',
    'alksjdlkasjd',
    '',
    '',
    ''
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `documentos_paciente`
--

DROP TABLE IF EXISTS `documentos_paciente`;
CREATE TABLE IF NOT EXISTS `documentos_paciente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL COMMENT 'Chave estrangeira para user_pacientes.id',
  `id_consulta` int(11) DEFAULT NULL COMMENT 'Chave estrangeira para vincular o documento a uma consulta específica (opcional).',
  `titulo_documento` varchar(255) NOT NULL COMMENT 'Ex: Raio-X do Tórax - 05/08/2025',
  `tipo_documento` varchar(100) DEFAULT NULL COMMENT 'Ex: Raio-X, Exame de Sangue, Eletrocardiograma',
  `nome_arquivo` varchar(255) NOT NULL COMMENT 'Nome do arquivo salvo no servidor (ex: paciente_15_raiox_timestamp.pdf)',
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `nome_documento` varchar(255) DEFAULT NULL,
  `observacoes` varchar(255) DEFAULT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_consulta` (`id_consulta`)
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `documentos_paciente`:
--   `id_paciente`
--       `user_pacientes` -> `id`
--   `id_consulta`
--       `consultas` -> `id_consulta`
--

--
-- Despejando dados para a tabela `documentos_paciente`
--

INSERT INTO `documentos_paciente` (
    `id`,
    `id_paciente`,
    `id_consulta`,
    `titulo_documento`,
    `tipo_documento`,
    `nome_arquivo`,
    `data_upload`,
    `nome_documento`,
    `observacoes`,
    `caminho_arquivo`
  )
VALUES (
    1,
    6,
    NULL,
    '',
    NULL,
    '',
    '2025-08-25 22:16:56',
    'Captura de Tela (13).png',
    'jyfglkglkhçohoihpohy lhçohçipih',
    'uploads/consultas_anexos/consulta13_68ace0d808d46_Captura de Tela (13).png'
  ),
  (
    2,
    5,
    14,
    '',
    NULL,
    '',
    '2025-08-25 22:31:30',
    'Captura de tela 2025-06-25 111939.png',
    'Captura de tela de um site',
    'uploads/consultas_anexos/consulta14_68ace442998ad_Captura de tela 2025-06-25 111939.png'
  ),
  (
    3,
    5,
    15,
    'Captura de tela 2025-06-25 111939.png',
    NULL,
    'consulta15_68ace4e70cf84_Captura de tela 2025-06-25 111939.png',
    '2025-08-25 22:34:15',
    NULL,
    'Captura de tela de um site',
    'uploads/consultas_anexos/consulta15_68ace4e70cf84_Captura de tela 2025-06-25 111939.png'
  ),
  (
    4,
    6,
    16,
    'Captura de Tela (13).png',
    NULL,
    'consulta16_68ace5822875a_Captura de Tela (13).png',
    '2025-08-25 22:36:50',
    NULL,
    'asçkdhlkashdlbaslkjd asdpçjjlsdçljasçldja spajdçlasjdçasdj asldknçasjd',
    'uploads/consultas_anexos/consulta16_68ace5822875a_Captura de Tela (13).png'
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `historico_visitas`
--

DROP TABLE IF EXISTS `historico_visitas`;
CREATE TABLE IF NOT EXISTS `historico_visitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_paciente` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `genero` enum('M', 'F', 'Outro') DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `data_visita` date DEFAULT NULL,
  `hora_consulta` time DEFAULT NULL,
  `motivo_visita` text DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `forma_pagamento` enum('Cartão', 'Débito', 'PIX', 'Dinheiro') DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `crm_medico` varchar(20) DEFAULT NULL,
  `nome_medico` varchar(100) DEFAULT NULL,
  `telefone_medico` varchar(20) DEFAULT NULL,
  `email_medico` varchar(100) DEFAULT NULL,
  `genero_medico` enum('M', 'F', 'Outro') DEFAULT NULL,
  `data_nascimento_medico` date DEFAULT NULL,
  `ano_formacao` year(4) DEFAULT NULL,
  `status_atual` enum('ativo', 'inativo') DEFAULT NULL,
  `cidade_medico` varchar(100) DEFAULT NULL,
  `estado_medico` varchar(100) DEFAULT NULL,
  `endereco_medico` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `historico_visitas`:
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `hospitais`
--

DROP TABLE IF EXISTS `hospitais`;
CREATE TABLE IF NOT EXISTS `hospitais` (
  `id_hospital` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` text NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_hospital`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `hospitais`:
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `medicamentos_log`
--

DROP TABLE IF EXISTS `medicamentos_log`;
CREATE TABLE IF NOT EXISTS `medicamentos_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_prescricao` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `data_hora_tomado` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_prescricao` (`id_prescricao`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `medicamentos_log`:
--   `id_prescricao`
--       `medicamentos_prescritos` -> `id`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `medicamentos_prescritos`
--

DROP TABLE IF EXISTS `medicamentos_prescritos`;
CREATE TABLE IF NOT EXISTS `medicamentos_prescritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `nome_medicamento` varchar(255) NOT NULL,
  `dosagem` varchar(100) NOT NULL,
  `frequencia` varchar(255) NOT NULL,
  `horario_programado` time NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `medicamentos_prescritos`:
--   `id_paciente`
--       `user_pacientes` -> `id`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `medicos_hospitais`
--

DROP TABLE IF EXISTS `medicos_hospitais`;
CREATE TABLE IF NOT EXISTS `medicos_hospitais` (
  `medico_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  PRIMARY KEY (`medico_id`, `hospital_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `medicos_hospitais`:
--   `medico_id`
--       `user_medicos` -> `id`
--   `hospital_id`
--       `hospitais` -> `id_hospital`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `metas_saude`
--

DROP TABLE IF EXISTS `metas_saude`;
CREATE TABLE IF NOT EXISTS `metas_saude` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `descricao_meta` varchar(255) NOT NULL,
  `frequencia` enum('diaria', 'semanal') NOT NULL DEFAULT 'diaria',
  `status` enum('ativa', 'concluida') NOT NULL DEFAULT 'ativa',
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `metas_saude`:
--   `id_paciente`
--       `user_pacientes` -> `id`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `metas_saude_log`
--

DROP TABLE IF EXISTS `metas_saude_log`;
CREATE TABLE IF NOT EXISTS `metas_saude_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_meta` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `data_conclusao` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_meta` (`id_meta`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `metas_saude_log`:
--   `id_meta`
--       `metas_saude` -> `id`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `prontuarios`
--

DROP TABLE IF EXISTS `prontuarios`;
CREATE TABLE IF NOT EXISTS `prontuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `data_consulta` date NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `exames` text DEFAULT NULL,
  `tratamento` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `medico_id` (`medico_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `prontuarios`:
--   `paciente_id`
--       `user_pacientes` -> `id`
--   `medico_id`
--       `user_medicos` -> `id`
--

-- --------------------------------------------------------
--
-- Estrutura para tabela `sinais_vitais`
--

DROP TABLE IF EXISTS `sinais_vitais`;
CREATE TABLE IF NOT EXISTS `sinais_vitais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `id_consulta` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'Ex: pressao_arterial, glicemia, peso',
  `valor1` varchar(50) NOT NULL,
  `valor2` varchar(50) DEFAULT NULL,
  `unidade` varchar(20) NOT NULL,
  `data_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`),
  KEY `idx_id_consulta` (`id_consulta`)
) ENGINE = InnoDB AUTO_INCREMENT = 32 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `sinais_vitais`:
--   `id_consulta`
--       `consultas` -> `id_consulta`
--   `id_paciente`
--       `user_pacientes` -> `id`
--

--
-- Despejando dados para a tabela `sinais_vitais`
--

INSERT INTO `sinais_vitais` (
    `id`,
    `id_paciente`,
    `id_consulta`,
    `tipo`,
    `valor1`,
    `valor2`,
    `unidade`,
    `data_registro`,
    `observacoes`
  )
VALUES (
    9,
    5,
    14,
    'pa',
    '120/80',
    NULL,
    'mmHg',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    10,
    5,
    14,
    'fc',
    '72',
    NULL,
    'bpm',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    11,
    5,
    14,
    'fr',
    '16',
    NULL,
    'irpm',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    12,
    5,
    14,
    'temp',
    '36.5',
    NULL,
    'ºC',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    13,
    5,
    14,
    'spo2',
    '98',
    NULL,
    '%',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    14,
    5,
    14,
    'peso',
    '86',
    NULL,
    'KG',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    15,
    5,
    14,
    'altura',
    '170',
    NULL,
    '',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    16,
    5,
    14,
    'imc',
    '29.8/',
    NULL,
    'kgm²',
    '2025-08-25 19:31:30',
    NULL
  ),
  (
    17,
    5,
    15,
    'pa',
    '120/80',
    NULL,
    'mmHg',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    18,
    5,
    15,
    'fc',
    '72',
    NULL,
    'bpm',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    19,
    5,
    15,
    'fr',
    '16',
    NULL,
    'irpm',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    20,
    5,
    15,
    'temp',
    '36.5',
    NULL,
    'ºC',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    21,
    5,
    15,
    'spo2',
    '98',
    NULL,
    '%',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    22,
    5,
    15,
    'peso',
    '86',
    NULL,
    'KG',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    23,
    5,
    15,
    'altura',
    '170',
    NULL,
    '',
    '2025-08-25 19:34:15',
    NULL
  ),
  (
    24,
    6,
    16,
    'pa',
    '120/82',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    25,
    6,
    16,
    'fc',
    '72',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    26,
    6,
    16,
    'fr',
    '16',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    27,
    6,
    16,
    'temp',
    '36.5',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    28,
    6,
    16,
    'spo2',
    '98',
    NULL,
    '%',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    29,
    6,
    16,
    'peso',
    '98',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    30,
    6,
    16,
    'altura',
    '168',
    NULL,
    '',
    '2025-08-25 19:36:50',
    NULL
  ),
  (
    31,
    6,
    16,
    'imc',
    '34.7',
    NULL,
    'kg/m²',
    '2025-08-25 19:36:50',
    NULL
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `user_medicos`
--

DROP TABLE IF EXISTS `user_medicos`;
CREATE TABLE IF NOT EXISTS `user_medicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone_medico` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` enum('M', 'F', 'Outro') DEFAULT NULL,
  `crm` varchar(20) NOT NULL,
  `ano_formacao` year(4) DEFAULT NULL,
  `status_atual` enum('ativo', 'inativo') DEFAULT 'ativo',
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefone` int(11) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm` (`crm`),
  UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `user_medicos`:
--

--
-- Despejando dados para a tabela `user_medicos`
--

INSERT INTO `user_medicos` (
    `id`,
    `nome`,
    `email`,
    `telefone_medico`,
    `data_nascimento`,
    `genero`,
    `crm`,
    `ano_formacao`,
    `status_atual`,
    `cidade`,
    `estado`,
    `endereco`,
    `senha`,
    `criado_em`,
    `telefone`,
    `foto_perfil`,
    `especialidade`
  )
VALUES (
    1,
    'Rondineli Da Silva Oliveira',
    'rondinelidev@outlook.com',
    '21977395867',
    '0000-00-00',
    'M',
    '071692',
    '2008',
    'ativo',
    'Teresópolis',
    'RJ',
    'Rua Alameda Monte Castelo',
    '$2y$10$QbZbOqWVFLH4A4StdMqbsuU2as/TrliqhVsA76Y1FW6PhDoVk26MW',
    '2025-08-05 13:13:58',
    2147483647,
    'medico_1_1755827458.jpg',
    NULL
  ),
  (
    2,
    '',
    'teste@teste.com',
    NULL,
    NULL,
    NULL,
    '16928',
    NULL,
    'ativo',
    NULL,
    'SP',
    NULL,
    '$2y$10$x9y8Umtg3ACixDus175d2uXa8EpIuk062Klr5PxGdUNPCEtfSRv8y',
    '2025-08-12 18:57:11',
    NULL,
    NULL,
    NULL
  );
-- --------------------------------------------------------
--
-- Estrutura para tabela `user_pacientes`
--

DROP TABLE IF EXISTS `user_pacientes`;
CREATE TABLE IF NOT EXISTS `user_pacientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_paciente` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `cartao_sus` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `nome_mae` varchar(255) DEFAULT NULL,
  `genero` enum('M', 'F', 'Outro') DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `endereco` varchar(255) DEFAULT NULL,
  `convenio` varchar(100) DEFAULT NULL,
  `profissao` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `rg` (`rg`),
  UNIQUE KEY `cartao_sus` (`cartao_sus`)
) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- RELACIONAMENTOS PARA TABELAS `user_pacientes`:
--

--
-- Despejando dados para a tabela `user_pacientes`
--

INSERT INTO `user_pacientes` (
    `id`,
    `nome_paciente`,
    `cpf`,
    `rg`,
    `cartao_sus`,
    `telefone`,
    `data_nascimento`,
    `sexo`,
    `estado_civil`,
    `nome_mae`,
    `genero`,
    `senha`,
    `criado_em`,
    `endereco`,
    `convenio`,
    `profissao`,
    `email`,
    `foto_perfil`
  )
VALUES (
    5,
    'Rondineli Oliveira',
    '169.974.247-24',
    NULL,
    NULL,
    '(21)97739-5867',
    '1995-07-12',
    NULL,
    NULL,
    'Maria Aparecida da Silva Oliveira',
    '',
    '$2y$10$S.1XvWAqk5ncF9PNzf7gfubPINIdWmka4/WlrsJ4ZKIXdlcgqdSoi',
    '2025-08-20 17:49:28',
    'Alameda Monte Castelo, 182 - Bl 4 Apto 501',
    'SUS',
    'Barbeiro',
    'rondi.rio@hotmail.com',
    'paciente_5_1755712452.jpg'
  ),
  (
    6,
    'Antonio Couto Vitória',
    '356.867.667-20',
    NULL,
    NULL,
    '(21)99269-6100',
    '1954-10-16',
    NULL,
    NULL,
    'Alzira Couto',
    '',
    '$2y$10$p3xKvoUj0P5OyjETbcX8EO5X6vQ4yfEKb9X8YsfgrB/uyJUqzbLDS',
    '2025-08-22 12:07:33',
    'Alameda Monte Castelo, 182 - Apto 501',
    'Unimed - RJ',
    'Aposentado',
    'toni.rio@hotmail.com',
    'paciente_6_1755864729.jpg'
  );
--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alergias`
--
ALTER TABLE `alergias`
ADD CONSTRAINT `alergias_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `consultas`
--
ALTER TABLE `consultas`
ADD CONSTRAINT `fk_consulta_medico` FOREIGN KEY (`id_medico`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_consulta_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE
SET NULL,
  ADD CONSTRAINT `fk_consultas_paciente_id` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE
SET NULL;
--
-- Restrições para tabelas `consultas_anamnese`
--
ALTER TABLE `consultas_anamnese`
ADD CONSTRAINT `fk_anamnese_consulta` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`) ON DELETE CASCADE;
--
-- Restrições para tabelas `consultas_exame_fisico`
--
ALTER TABLE `consultas_exame_fisico`
ADD CONSTRAINT `fk_exame_fisico_consulta` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`) ON DELETE CASCADE;
--
-- Restrições para tabelas `documentos_paciente`
--
ALTER TABLE `documentos_paciente`
ADD CONSTRAINT `documentos_paciente_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_paciente_ibfk_2` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`) ON DELETE
SET NULL;
--
-- Restrições para tabelas `medicamentos_log`
--
ALTER TABLE `medicamentos_log`
ADD CONSTRAINT `medicamentos_log_ibfk_1` FOREIGN KEY (`id_prescricao`) REFERENCES `medicamentos_prescritos` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `medicamentos_prescritos`
--
ALTER TABLE `medicamentos_prescritos`
ADD CONSTRAINT `medicamentos_prescritos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `medicos_hospitais`
--
ALTER TABLE `medicos_hospitais`
ADD CONSTRAINT `medicos_hospitais_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicos_hospitais_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitais` (`id_hospital`) ON DELETE CASCADE;
--
-- Restrições para tabelas `metas_saude`
--
ALTER TABLE `metas_saude`
ADD CONSTRAINT `metas_saude_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `metas_saude_log`
--
ALTER TABLE `metas_saude_log`
ADD CONSTRAINT `metas_saude_log_ibfk_1` FOREIGN KEY (`id_meta`) REFERENCES `metas_saude` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `prontuarios`
--
ALTER TABLE `prontuarios`
ADD CONSTRAINT `prontuarios_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prontuarios_ibfk_2` FOREIGN KEY (`medico_id`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE;
--
-- Restrições para tabelas `sinais_vitais`
--
ALTER TABLE `sinais_vitais`
ADD CONSTRAINT `fk_sinais_vitais_consulta` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`) ON DELETE CASCADE,
  ADD CONSTRAINT `sinais_vitais_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE;
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;