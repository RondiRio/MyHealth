-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/08/2025 às 16:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `myhealth`
--
CREATE DATABASE IF NOT EXISTS `myhealth` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `myhealth`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultas`
--
-- Criação: 06/08/2025 às 13:37
--

DROP TABLE IF EXISTS `consultas`;
CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL,
  `paciente_id` int(11) DEFAULT NULL COMMENT 'ID do paciente, se ele tiver cadastro no sistema (opcional).',
  `id_medico` int(11) NOT NULL COMMENT 'ID do médico que realizou a consulta.',
  `id_hospital` int(11) DEFAULT NULL COMMENT 'ID do hospital onde a consulta foi realizada (opcional).',
  `cpf_paciente` varchar(14) NOT NULL,
  `nome_paciente` varchar(255) NOT NULL,
  `data_consulta` datetime NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `status_consulta` enum('realizada','agendada','cancelada') DEFAULT 'realizada',
  `anamnese` text DEFAULT NULL COMMENT 'Queixas e histórico do paciente.',
  `exame_fisico` text DEFAULT NULL COMMENT 'Observações do exame físico.',
  `hipotese_diagnostica` text DEFAULT NULL COMMENT 'Suspeitas e diagnósticos diferenciais.',
  `diagnostico_final` text DEFAULT NULL COMMENT 'Diagnóstico confirmado.',
  `tratamento_proposto` text DEFAULT NULL COMMENT 'Prescrições, receitas e recomendações.',
  `observacoes_privadas` text DEFAULT NULL COMMENT 'Anotações internas do médico, não visíveis ao paciente.',
  `visivel_para_paciente` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se TRUE (1), o paciente pode ver este registro.',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `consultas` (`id_consulta`, `paciente_id`, `id_medico`, `id_hospital`, `cpf_paciente`, `nome_paciente`, `data_consulta`, `especialidade`, `status_consulta`, `anamnese`, `exame_fisico`, `hipotese_diagnostica`, `diagnostico_final`, `tratamento_proposto`, `observacoes_privadas`, `visivel_para_paciente`, `criado_em`) VALUES
(1, NULL, 1, NULL, '35686766720', 'Antonio Couto Vitória', '2025-08-05 12:04:00', NULL, 'realizada', 'sdaljfkafd laksdhflafh', 'saldkfhlafd', NULL, 'slkdflkasdf', 'laskdjflaksd', 'laksdfjslçkadfj', 1, '2025-08-05 16:17:13'),
(2, NULL, 1, NULL, '12345678925', 'Joel Elias de Sá', '2025-08-05 17:42:00', NULL, 'realizada', 'sljfdkhsdlfh', 'laskdhflskdhf', NULL, 'lakshfdlasdh', 'lakshdfalksdh', 'lakshfdlaksdh', 1, '2025-08-05 20:45:19'),
(3, NULL, 1, NULL, '12345678912', 'Marcos José da Cunha', '2025-08-05 17:52:00', 'Clinico Geral', 'realizada', 'Paciente chegou a unidade com reclamações de dores de cabeça e tontura', 'Paciente Possui um hematoma na parte frontal da cabeça', 'Hemorragia Interna', 'Hemorragia Interna', 'Internação e abertura do Crâneo para remoção do liquido e nova visita para avaliar paciente.', 'O Paciente se não tratado nas próximas 24h tem alto perigo de ir a óbito.', 1, '2025-08-05 20:55:32'),
(4, NULL, 1, NULL, '169.974.247-24', 'Rondineli Da Silva Oliveira Moreira', '2025-08-06 01:35:00', NULL, 'realizada', 'Paciente sente insonia', '', NULL, 'Paciente precisa repousar', '', '', 1, '2025-08-06 04:37:23');

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_paciente`
--
-- Criação: 06/08/2025 às 04:14
--

DROP TABLE IF EXISTS `documentos_paciente`;
CREATE TABLE `documentos_paciente` (
  `id` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL COMMENT 'Chave estrangeira para user_pacientes.id',
  `id_consulta` int(11) DEFAULT NULL COMMENT 'Chave estrangeira para vincular o documento a uma consulta específica (opcional).',
  `titulo_documento` varchar(255) NOT NULL COMMENT 'Ex: Raio-X do Tórax - 05/08/2025',
  `tipo_documento` varchar(100) DEFAULT NULL COMMENT 'Ex: Raio-X, Exame de Sangue, Eletrocardiograma',
  `nome_arquivo` varchar(255) NOT NULL COMMENT 'Nome do arquivo salvo no servidor (ex: paciente_15_raiox_timestamp.pdf)',
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `documentos_paciente`:
--   `id_paciente`
--       `user_pacientes` -> `id`
--   `id_consulta`
--       `consultas` -> `id_consulta`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_visitas`
--
-- Criação: 04/08/2025 às 13:48
--

DROP TABLE IF EXISTS `historico_visitas`;
CREATE TABLE `historico_visitas` (
  `id` int(11) NOT NULL,
  `nome_paciente` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `genero` enum('M','F','Outro') DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `data_visita` date DEFAULT NULL,
  `hora_consulta` time DEFAULT NULL,
  `motivo_visita` text DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `forma_pagamento` enum('Cartão','Débito','PIX','Dinheiro') DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `crm_medico` varchar(20) DEFAULT NULL,
  `nome_medico` varchar(100) DEFAULT NULL,
  `telefone_medico` varchar(20) DEFAULT NULL,
  `email_medico` varchar(100) DEFAULT NULL,
  `genero_medico` enum('M','F','Outro') DEFAULT NULL,
  `data_nascimento_medico` date DEFAULT NULL,
  `ano_formacao` year(4) DEFAULT NULL,
  `status_atual` enum('ativo','inativo') DEFAULT NULL,
  `cidade_medico` varchar(100) DEFAULT NULL,
  `estado_medico` varchar(100) DEFAULT NULL,
  `endereco_medico` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `historico_visitas`:
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `hospitais`
--
-- Criação: 04/08/2025 às 13:47
--

DROP TABLE IF EXISTS `hospitais`;
CREATE TABLE `hospitais` (
  `id_hospital` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` text NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `hospitais`:
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `medicos_hospitais`
--
-- Criação: 04/08/2025 às 13:47
--

DROP TABLE IF EXISTS `medicos_hospitais`;
CREATE TABLE `medicos_hospitais` (
  `medico_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `medicos_hospitais`:
--   `medico_id`
--       `user_medicos` -> `id`
--   `hospital_id`
--       `hospitais` -> `id_hospital`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `prontuarios`
--
-- Criação: 04/08/2025 às 13:47
--

DROP TABLE IF EXISTS `prontuarios`;
CREATE TABLE `prontuarios` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `data_consulta` date NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `exames` text DEFAULT NULL,
  `tratamento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `prontuarios`:
--   `paciente_id`
--       `user_pacientes` -> `id`
--   `medico_id`
--       `user_medicos` -> `id`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_medicos`
--
-- Criação: 05/08/2025 às 13:32
-- Última atualização: 05/08/2025 às 14:54
--

DROP TABLE IF EXISTS `user_medicos`;
CREATE TABLE `user_medicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone_medico` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` enum('M','F','Outro') DEFAULT NULL,
  `crm` varchar(20) NOT NULL,
  `ano_formacao` year(4) DEFAULT NULL,
  `status_atual` enum('ativo','inativo') DEFAULT 'ativo',
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefone` int(11) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `user_medicos`:
--

--
-- Despejando dados para a tabela `user_medicos`
--

INSERT INTO `user_medicos` (`id`, `nome`, `email`, `telefone_medico`, `data_nascimento`, `genero`, `crm`, `ano_formacao`, `status_atual`, `cidade`, `estado`, `endereco`, `senha`, `criado_em`, `telefone`, `foto_perfil`) VALUES
(1, 'Rondineli Da Silva Oliveira', 'teste@teste.com', '21977395867', '0000-00-00', 'M', '071692', '2008', 'ativo', 'Teresópolis', 'RJ', 'Rua Alameda Monte Castelo', '$2y$10$QbZbOqWVFLH4A4StdMqbsuU2as/TrliqhVsA76Y1FW6PhDoVk26MW', '2025-08-05 13:13:58', 2147483647, 'medico_1_1754404410.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_pacientes`
--
-- Criação: 06/08/2025 às 13:41
-- Última atualização: 06/08/2025 às 13:50
--

DROP TABLE IF EXISTS `user_pacientes`;
CREATE TABLE `user_pacientes` (
  `id` int(11) NOT NULL,
  `nome_paciente` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `cartao_sus` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `nome_mae` varchar(255) DEFAULT NULL,
  `genero` enum('M','F','Outro') DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `endereco` varchar(255) DEFAULT NULL,
  `convenio` varchar(100) DEFAULT NULL,
  `profissao` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONAMENTOS PARA TABELAS `user_pacientes`:
--

--
-- Despejando dados para a tabela `user_pacientes`
--

INSERT INTO `user_pacientes` (`id`, `nome_paciente`, `cpf`, `rg`, `cartao_sus`, `telefone`, `data_nascimento`, `sexo`, `estado_civil`, `nome_mae`, `genero`, `senha`, `criado_em`, `endereco`, `convenio`, `profissao`, `email`, `foto_perfil`) VALUES
(1, 'Rondineli Da Silva Oliveira', '169.974.274-24', NULL, NULL, '(21)97793-5867', '0000-00-00', NULL, NULL, NULL, NULL, '$2y$10$zJ6D/pGvwovhS8OXjkzl0ud.3dfDQjBo.Ww4FeAhR5xXwmT1WtpbO', '2025-08-06 12:06:26', 'Rua Alameda Monte Castelo', NULL, NULL, 'testerondi@teste.com', 'paciente_1_1754488202.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `id_medico` (`id_medico`),
  ADD KEY `id_hospital` (`id_hospital`);

--
-- Índices de tabela `documentos_paciente`
--
ALTER TABLE `documentos_paciente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_consulta` (`id_consulta`);

--
-- Índices de tabela `historico_visitas`
--
ALTER TABLE `historico_visitas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `hospitais`
--
ALTER TABLE `hospitais`
  ADD PRIMARY KEY (`id_hospital`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `medicos_hospitais`
--
ALTER TABLE `medicos_hospitais`
  ADD PRIMARY KEY (`medico_id`,`hospital_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Índices de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `medico_id` (`medico_id`);

--
-- Índices de tabela `user_medicos`
--
ALTER TABLE `user_medicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm` (`crm`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `user_pacientes`
--
ALTER TABLE `user_pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `rg` (`rg`),
  ADD UNIQUE KEY `cartao_sus` (`cartao_sus`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `documentos_paciente`
--
ALTER TABLE `documentos_paciente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_visitas`
--
ALTER TABLE `historico_visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `hospitais`
--
ALTER TABLE `hospitais`
  MODIFY `id_hospital` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `user_medicos`
--
ALTER TABLE `user_medicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `user_pacientes`
--
ALTER TABLE `user_pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `fk_consulta_medico` FOREIGN KEY (`id_medico`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_consulta_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_consultas_paciente_id` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `documentos_paciente`
--
ALTER TABLE `documentos_paciente`
  ADD CONSTRAINT `documentos_paciente_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_paciente_ibfk_2` FOREIGN KEY (`id_consulta`) REFERENCES `consultas` (`id_consulta`) ON DELETE SET NULL;

--
-- Restrições para tabelas `medicos_hospitais`
--
ALTER TABLE `medicos_hospitais`
  ADD CONSTRAINT `medicos_hospitais_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicos_hospitais_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitais` (`id_hospital`) ON DELETE CASCADE;

--
-- Restrições para tabelas `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD CONSTRAINT `prontuarios_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `user_pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prontuarios_ibfk_2` FOREIGN KEY (`medico_id`) REFERENCES `user_medicos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
