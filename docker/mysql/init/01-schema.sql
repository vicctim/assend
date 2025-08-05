-- Configurações iniciais
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;

-- Criação da tabela de administradores
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de instituições
CREATE TABLE IF NOT EXISTS instituicoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    logo VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    curso VARCHAR(255) NOT NULL,
    periodo VARCHAR(50) NOT NULL,
    instituicao_id INT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE,
    INDEX idx_instituicao (instituicao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de perguntas
CREATE TABLE IF NOT EXISTS perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    categoria ENUM('facil', 'medio', 'dificil') NOT NULL,
    status ENUM('nao_respondida', 'respondida', 'pulada') DEFAULT 'nao_respondida',
    ativo BOOLEAN DEFAULT TRUE,
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de alternativas
CREATE TABLE IF NOT EXISTS alternativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta_id INT NOT NULL,
    letra CHAR(1) NOT NULL,
    texto VARCHAR(500) NOT NULL,
    correta BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE,
    INDEX idx_pergunta (pergunta_id),
    UNIQUE KEY uk_pergunta_letra (pergunta_id, letra)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de sorteios de instituições
CREATE TABLE IF NOT EXISTS sorteios_instituicoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instituicao_id INT NOT NULL,
    sorteado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de sorteios de alunos
CREATE TABLE IF NOT EXISTS sorteios_alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    instituicao_id INT NOT NULL,
    sorteado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de respostas
CREATE TABLE IF NOT EXISTS respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    pergunta_id INT NOT NULL,
    alternativa_id INT,
    correta BOOLEAN DEFAULT FALSE,
    tempo_resposta INT COMMENT 'Tempo em segundos',
    respondida_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE,
    FOREIGN KEY (alternativa_id) REFERENCES alternativas(id) ON DELETE SET NULL,
    INDEX idx_aluno (aluno_id),
    INDEX idx_pergunta (pergunta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de auditoria
CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50),
    acao VARCHAR(255) NOT NULL,
    tabela VARCHAR(50),
    registro_id INT,
    dados_anteriores TEXT,
    dados_novos TEXT,
    ip VARCHAR(45),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_tabela (tabela),
    INDEX idx_data (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de configurações
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    tipo ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    descricao VARCHAR(255),
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;