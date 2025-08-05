-- Criação da tabela de administradores
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

INSERT INTO admin (usuario, senha) VALUES ('pixfilmes', '$2y$10$w6Qw6Qw6Qw6Qw6Qw6Qw6eOQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6');
-- Senha: liveassend (hash gerado pelo password_hash do PHP)

-- Criação da tabela de perguntas
CREATE TABLE IF NOT EXISTS perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    categoria ENUM('facil', 'medio', 'dificil') NOT NULL,
    status ENUM('nao_respondida', 'respondida', 'pulada') DEFAULT 'nao_respondida',
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de alternativas
CREATE TABLE IF NOT EXISTS alternativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta_id INT NOT NULL,
    letra CHAR(1) NOT NULL,
    texto VARCHAR(255) NOT NULL,
    correta BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE
); 