# MyHealth - Sua Saúde em Suas Mãos

**MyHealth é uma plataforma web moderna e segura, projetada para capacitar os usuários a gerenciar de forma proativa seu histórico de saúde, agendamentos médicos e monitoramento de bem-estar em um só lugar.**

[![Status da Build](https://img.shields.io/badge/build-em%20desenvolvimento-yellow)](https://github.com/RondiRio/MyHealth)
[![Versão](https://img.shields.io/badge/version-1.0.0-informational)](https://github.com/RondiRio/MyHealth)

---

## 1. Visão Geral do Projeto

Em um mundo onde o acesso rápido e organizado às informações de saúde é crucial, o MyHealth surge como uma solução centralizada e intuitiva. A plataforma foi criada para ser o diário de saúde digital do usuário, permitindo o registro e acompanhamento de consultas médicas, exames, medicamentos, métricas de saúde (como pressão arterial e glicose) e muito mais.

O objetivo principal é substituir anotações em papel e múltiplas planilhas por uma única fonte de verdade, segura e acessível de qualquer dispositivo, ajudando os usuários a terem um papel mais ativo no cuidado com sua saúde e a fornecerem históricos precisos aos profissionais da área.

## 2. Funcionalidades Principais

O MyHealth foi projetado com um conjunto de módulos essenciais para uma gestão de saúde 360°.

* **Dashboard Central:**
    * Visão geral dos próximos agendamentos.
    * Lembretes de medicamentos a serem tomados.
    * Gráficos rápidos das últimas métricas de saúde registradas.
* **Gerenciamento de Consultas e Exames:**
    * Agende e visualize o histórico de todas as suas consultas médicas e exames.
    * Anexe documentos e resultados de exames (PDFs, imagens) a cada registro.
    * Receba notificações e lembretes para os próximos compromissos.
* **Controle de Medicamentos:**
    * Cadastre todos os seus medicamentos, incluindo dosagem, frequência e duração do tratamento.
    * Configure alarmes e lembretes para nunca mais esquecer de tomar um remédio.
    * Mantenha um histórico de todos os medicamentos já utilizados.
* **Monitoramento de Métricas de Saúde:**
    * Registre e acompanhe métricas vitais como:
        * Pressão Arterial (Sistólica e Diastólica)
        * Níveis de Glicose no Sangue
        * Peso e Altura (cálculo automático de IMC)
        * Frequência Cardíaca
    * Visualize a evolução de cada métrica em gráficos interativos ao longo do tempo.
* **Histórico Médico Unificado:**
    * Registre informações importantes como alergias, condições crônicas e histórico de cirurgias.
    * Tenha um perfil de saúde completo e exportável para compartilhar com seus médicos.
* **Segurança e Privacidade:**
    * Autenticação segura de usuários.
    * Todos os dados são privados e acessíveis apenas pelo respectivo usuário.

---

## 3. Arquitetura e Tecnologias

O MyHealth foi construído utilizando uma arquitetura moderna, com um backend robusto servindo uma API RESTful para um frontend dinâmico e interativo (SPA - Single Page Application).

* **Backend (API):**
    * **Linguagem:** **PHP 8.4**
  
    * **Acesso a Dados:** Eloquent ORM (do Laravel) para interações seguras com o banco de dados.
    * **Autenticação:** **JWT (JSON Web Tokens)** para proteger os endpoints da API.
* **Frontend (Aplicação Web):**
    * **Linguagens:** JavaScript (ES6+), JSX, HTML5, CSS3
    * **Visualização de Dados:** **Chart.js** para a criação dos gráficos de métricas de saúde.
* **Banco de Dados:**
    * **SGBD:** **MySQL 8**
* **Ambiente de Desenvolvimento:**

```
+----------------------------------+
|      Frontend (HTML)      |
|    (Executando no Navegador)     |
+----------------------------------+
                ^
                | API Calls (JSON via HTTPS)
                v
+----------------------------------+
|     Backend API (PHP)    |
|                                  |
|   - Autenticação JWT             |
|   - Regras de Negócio            |
|   - Endpoints RESTful            |
|                                  |
+----------------------------------+
                ^
                | SQL Queries (via ORM)
                v
+----------------------------------+
|      Banco de Dados (MySQL)      |
+----------------------------------+
```

---

## 4. Esquema do Banco de Dados

A estrutura do banco de dados foi projetada para ser relacional, garantindo a integridade e a organização dos dados de saúde do usuário.
**Por fins comerciais e de direitos, o esquema de banco de dados não foi cincluido completamente neste readme.md**

```sql
-- Tabela principal de usuários da plataforma
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    data_nascimento DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para armazenar consultas médicas e exames
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL, -- Ex: "Consulta com Cardiologista"
    tipo ENUM('Consulta', 'Exame') NOT NULL,
    especialidade VARCHAR(100),
    data_hora DATETIME NOT NULL,
    local VARCHAR(255),
    status ENUM('Agendado', 'Realizado', 'Cancelado') DEFAULT 'Agendado',
    observacoes TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela para anexos, como resultados de exames
CREATE TABLE anexos_agendamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(512) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
);

-- Tabela para controle de medicamentos
CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome_medicamento VARCHAR(255) NOT NULL,
    dosagem VARCHAR(100), -- Ex: "500mg"
    frequencia VARCHAR(100), -- Ex: "A cada 8 horas"
    data_inicio DATE,
    data_fim DATE,
    ativo BOOLEAN DEFAULT true,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela para registrar as métricas de saúde
CREATE TABLE metricas_saude (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_metrica VARCHAR(50) NOT NULL, -- 'pressao_arterial', 'glicose', 'peso'
    valor1 DECIMAL(10, 2) NOT NULL, -- Para Pressão Sistólica, Glicose, Peso
    valor2 DECIMAL(10, 2), -- Para Pressão Diastólica
    unidade VARCHAR(20), -- 'mmHg', 'mg/dL', 'kg'
    data_registro DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela para o histórico médico geral do usuário
CREATE TABLE historico_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    alergias TEXT,
    condicoes_cronicas TEXT,
    cirurgias_previas TEXT,
    tipo_sanguineo VARCHAR(5),
    UNIQUE(usuario_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

```

---

## 5. Licença

Este projeto **é da NetoNerd SI, vedado o clone e compartilhamento sem altorização. Sugeito às penas judiciais.**.
