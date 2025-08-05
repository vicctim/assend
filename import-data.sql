-- Importar dados do arquivo original sql_live_pixfilm.sql

-- Inserir instituições
INSERT INTO instituicoes (id, nome, logo) VALUES
(1, 'UFU', NULL),
(2, 'UNIARAXÁ', NULL),
(3, 'IFTM', NULL),
(4, 'CEFET', NULL),
(5, 'PARTICIPANTE INDIVIDUAL', NULL);

-- Inserir alunos
INSERT INTO alunos (id, nome, curso, periodo, instituicao_id) VALUES
(1, 'Pedro Henrique Cunha Nunes', 'ENGENHARIA CIVIL', 'Não informado', 1),
(2, 'Smyle Ormondes Quirino Gonçalves', 'ENGENHARIA CIVIL', 'Não informado', 1),
(3, 'Pedro Augusto Sousa Bessa', 'ENGENHARIA CIVIL', 'Não informado', 1),
(4, 'Matheus Borges Camargos', 'AGRONOMIA', 'Não informado', 2),
(7, 'Aneska Senna Souza', 'ENGENHARIA CIVIL', 'Não informado', 2),
(8, 'Enry Dhanton Veloso Hipólito Oliveira', 'ENGENHARIA CIVIL', 'Não informado', 2),
(9, 'Gabriela Brito Januário', 'ENGENHARIA CIVIL', 'Não informado', 2),
(10, 'Karla Elizabeth Magno', 'ENGENHARIA CIVIL', 'Não informado', 2),
(11, 'Márcio Roberto Pereira Filho', 'ENGENHARIA CIVIL', 'Não informado', 2),
(12, 'Estela Rezende Goulart', 'ENGENHARIA MECÂNICA', 'Não informado', 2),
(13, 'João Pedro Rezende Melo', 'ENGENHARIA MECÂNICA', 'Não informado', 2),
(14, 'José Roberto Borges', 'ENGENHARIA CIVIL', 'Não informado', 2),
(15, 'Wellerson Loureno Borges', 'ENGENHARIA MECÂNICA', 'Não informado', 2),
(16, 'Douglas de Morais', 'ENGENHARIA CIVIL', 'Não informado', 2),
(18, 'Tainara Alves de Oliveira', 'ENGENHARIA AGRONÔMICA', 'Não informado', 3),
(19, 'José Victor Silva Freitas', 'ENGENHARIA AGRONÔMICA', 'Não informado', 3),
(20, 'Rafael José da Silva Galvão', 'ENGENHARIA AGRONÔMICA', 'Não informado', 3),
(21, 'Mateus Moreira de Almeida', 'ENGENHARIA MINAS', 'Não informado', 4),
(22, 'Giovana Aparecida Rosa', 'ENGENHARIA CIVIL', 'Não informado', 4),
(23, 'Thaiza Manoel Queiroz', 'ENGENHARIA MINAS', 'Não informado', 4),
(24, 'Marcos Davi Pereira Teixeira', 'ENGENHARIA CIVIL', 'Não informado', 4),
(25, 'Ana Cláudia Borges Menezes', 'ENGENHARIA CIVIL', 'Não informado', 4),
(26, 'Gabriela Karolaine Barreto Oliveira', 'ENGENHARIA CIVIL', 'Não informado', 4),
(28, 'Adrielly Daiene Fonseca Caetano', 'ENGENHARIA MINAS', 'Não informado', 4),
(29, 'Esteban Ondo Mangue Nchama', 'ENGENHARIA MINAS', 'Não informado', 4),
(32, 'Lavínia gabriela Simão Barros', 'ENGENHARIA CIVIL', 'Não informado', 5),
(33, 'Eder Anacleto', 'ENGENHARIA MECÂNICA', 'Não informado', 5),
(35, 'Márcio Guilherme Prates Magalhães', 'ENGENHARIA MECÂNICA', 'Não informado', 5),
(36, 'Carlos Roberto Oliveira', 'ENGENHARIA MECÂNICA', 'Não informado', 5),
(39, 'Samuel Sávio Brigido Oliveira', 'ENGENHARIA MECÂNICA', 'Não informado', 5),
(40, 'José Lucas da Silva', 'AGRONOMIA', 'Não informado', 5),
(41, 'Raphael Lellis Ferreira Ribeiro', 'AGRONOMIA', 'Não informado', 5),
(43, 'Ronald Luan Dutra de Paula', 'AGRONOMIA', 'Não informado', 5),
(44, 'Arthur Maximiliane Martins Silva', 'AGRONOMIA', 'Não informado', 5),
(45, 'Luana Filomena Borges', 'ENGENHARIA CIVIL', 'Não informado', 5);