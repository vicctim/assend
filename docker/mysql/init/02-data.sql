-- Inserir usuário admin padrão
-- Senha: admin123 (altere em produção!)
INSERT INTO admin (usuario, senha) VALUES 
('admin', '$2y$10$YmEzOTliMjBlNTYwNDllNOiB1N3WQh3dDMxJrPJhBTL7y8QKbBpxm');

-- Inserir configurações padrão
INSERT INTO configuracoes (chave, valor, tipo, descricao) VALUES
('tempo_pergunta', '30', 'number', 'Tempo em segundos para responder cada pergunta'),
('perguntas_por_categoria', '5', 'number', 'Número de perguntas por categoria'),
('mostrar_ranking', 'true', 'boolean', 'Exibir ranking ao final do quiz'),
('permitir_pular', 'true', 'boolean', 'Permitir pular perguntas');

-- Inserir algumas perguntas de exemplo
INSERT INTO perguntas (texto, categoria) VALUES
-- Fácil
('Qual é a capital do Brasil?', 'facil'),
('Quantos estados tem o Brasil?', 'facil'),
('Qual é o maior país da América do Sul?', 'facil'),
('Em que ano o Brasil foi descoberto?', 'facil'),
('Qual é a moeda oficial do Brasil?', 'facil'),
-- Médio
('Qual é a fórmula química da água?', 'medio'),
('Quantos planetas existem no Sistema Solar?', 'medio'),
('Quem pintou a Mona Lisa?', 'medio'),
('Em que ano aconteceu a Proclamação da República?', 'medio'),
('Qual é o maior oceano do mundo?', 'medio'),
-- Difícil
('Qual é a velocidade da luz no vácuo?', 'dificil'),
('Quantos ossos tem o corpo humano adulto?', 'dificil'),
('Qual é o elemento químico mais abundante no universo?', 'dificil'),
('Em que ano foi fundada a cidade de São Paulo?', 'dificil'),
('Qual é a distância média da Terra ao Sol?', 'dificil');

-- Inserir alternativas para as perguntas
-- Pergunta 1 (Capital do Brasil)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(1, 'A', 'São Paulo', FALSE),
(1, 'B', 'Rio de Janeiro', FALSE),
(1, 'C', 'Brasília', TRUE),
(1, 'D', 'Salvador', FALSE);

-- Pergunta 2 (Estados do Brasil)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(2, 'A', '26 estados', FALSE),
(2, 'B', '27 estados', TRUE),
(2, 'C', '28 estados', FALSE),
(2, 'D', '25 estados', FALSE);

-- Pergunta 3 (Maior país da América do Sul)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(3, 'A', 'Argentina', FALSE),
(3, 'B', 'Brasil', TRUE),
(3, 'C', 'Peru', FALSE),
(3, 'D', 'Colômbia', FALSE);

-- Pergunta 4 (Descobrimento do Brasil)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(4, 'A', '1492', FALSE),
(4, 'B', '1500', TRUE),
(4, 'C', '1502', FALSE),
(4, 'D', '1498', FALSE);

-- Pergunta 5 (Moeda do Brasil)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(5, 'A', 'Peso', FALSE),
(5, 'B', 'Dólar', FALSE),
(5, 'C', 'Real', TRUE),
(5, 'D', 'Euro', FALSE);

-- Pergunta 6 (Fórmula da água)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(6, 'A', 'H2O', TRUE),
(6, 'B', 'CO2', FALSE),
(6, 'C', 'O2', FALSE),
(6, 'D', 'H2O2', FALSE);

-- Pergunta 7 (Planetas do Sistema Solar)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(7, 'A', '7 planetas', FALSE),
(7, 'B', '8 planetas', TRUE),
(7, 'C', '9 planetas', FALSE),
(7, 'D', '10 planetas', FALSE);

-- Pergunta 8 (Mona Lisa)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(8, 'A', 'Pablo Picasso', FALSE),
(8, 'B', 'Vincent van Gogh', FALSE),
(8, 'C', 'Leonardo da Vinci', TRUE),
(8, 'D', 'Michelangelo', FALSE);

-- Pergunta 9 (Proclamação da República)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(9, 'A', '1888', FALSE),
(9, 'B', '1889', TRUE),
(9, 'C', '1890', FALSE),
(9, 'D', '1891', FALSE);

-- Pergunta 10 (Maior oceano)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(10, 'A', 'Atlântico', FALSE),
(10, 'B', 'Índico', FALSE),
(10, 'C', 'Pacífico', TRUE),
(10, 'D', 'Ártico', FALSE);

-- Pergunta 11 (Velocidade da luz)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(11, 'A', '299.792.458 m/s', TRUE),
(11, 'B', '150.000.000 m/s', FALSE),
(11, 'C', '400.000.000 m/s', FALSE),
(11, 'D', '200.000.000 m/s', FALSE);

-- Pergunta 12 (Ossos do corpo humano)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(12, 'A', '206 ossos', TRUE),
(12, 'B', '210 ossos', FALSE),
(12, 'C', '195 ossos', FALSE),
(12, 'D', '220 ossos', FALSE);

-- Pergunta 13 (Elemento mais abundante)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(13, 'A', 'Oxigênio', FALSE),
(13, 'B', 'Carbono', FALSE),
(13, 'C', 'Hidrogênio', TRUE),
(13, 'D', 'Hélio', FALSE);

-- Pergunta 14 (Fundação de São Paulo)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(14, 'A', '1554', TRUE),
(14, 'B', '1500', FALSE),
(14, 'C', '1600', FALSE),
(14, 'D', '1550', FALSE);

-- Pergunta 15 (Distância Terra-Sol)
INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES
(15, 'A', '100 milhões de km', FALSE),
(15, 'B', '150 milhões de km', TRUE),
(15, 'C', '200 milhões de km', FALSE),
(15, 'D', '250 milhões de km', FALSE);