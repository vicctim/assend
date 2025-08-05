-- Configurações de charset UTF-8
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;

-- Limpar dados existentes
DELETE FROM alternativas;
DELETE FROM perguntas WHERE id > 15;

-- Inserir perguntas corrigidas
INSERT INTO perguntas (id, texto, categoria, status, criada_em) VALUES
(16, 'Qual é a principal função do CREA?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(17, 'Qual é a importância do registro profissional no Crea para um engenheiro recém-formado?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(18, 'Qual é um dos deveres éticos fundamentais dos profissionais registrados no Sistema Confea/Crea?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(19, 'Quem pode solicitar a Anotação de Responsabilidade Técnica (ART) de um serviço técnico?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(20, 'O Confea e os Creas atuam em qual esfera administrativa no Brasil?', 'facil', 'pulada', '2025-05-16 02:47:46'),
(21, 'Qual é um dos benefícios de um profissional estar regularmente registrado no Crea?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(22, 'Qual é o principal instrumento de identificação profissional emitido pelo Crea para os engenheiros e outros profissionais registrados?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(23, 'Qual das seguintes situações exige a emissão de uma Anotação de Responsabilidade Técnica (ART)?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(24, 'Quem é o responsável técnico pela execução de uma obra de engenharia, devidamente registrado no Crea e com a ART quitada?', 'facil', 'nao_respondida', '2025-05-16 02:47:46'),
(25, 'Qual é um dos papéis do Confea em relação aos Creas?', 'facil', 'nao_respondida', '2025-05-16 02:47:46');

-- Inserir algumas alternativas de exemplo
INSERT INTO alternativas (id, pergunta_id, letra, texto, correta) VALUES
(101, 16, 'A', 'Emitir licenças ambientais', 0),
(102, 16, 'B', 'Fiscalizar o exercício profissional da engenharia', 1),
(103, 16, 'C', 'Emitir CPF', 0),
(104, 16, 'D', 'Organizar concursos públicos', 0),
(105, 17, 'A', 'Permite apenas a participação em eventos e congressos da área.', 0),
(106, 17, 'B', 'É opcional e não interfere no exercício da profissão.', 0),
(107, 17, 'C', 'É obrigatório para o exercício legal da profissão e emissão de documentos técnicos.', 1),
(108, 17, 'D', 'Garante um salário mínimo estabelecido por lei.', 0);