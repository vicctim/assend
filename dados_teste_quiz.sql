-- Inserir instituições de teste (com logo NULL)
INSERT INTO instituicoes (nome, logo) VALUES
('Instituto Federal de Minas Gerais', NULL),
('Universidade Federal de Uberlândia', NULL),
('Centro Universitário do Triângulo', NULL),
('Faculdade de Engenharia de Araxá', NULL);

-- Inserir alunos de teste (nome, curso, periodo, instituicao_id)
INSERT INTO alunos (nome, curso, periodo, instituicao_id) VALUES
-- IFMG
('Ana Clara Souza', 'Engenharia Civil', '7º', 1),
('Bruno Lima', 'Engenharia Elétrica', '5º', 1),
('Marina Lopes', 'Engenharia de Computação', '3º', 1),
('Pedro Henrique', 'Engenharia de Produção', '6º', 1),
('Juliana Castro', 'Engenharia Ambiental', '4º', 1),
-- UFU
('Carlos Eduardo', 'Engenharia de Produção', '6º', 2),
('Daniela Martins', 'Engenharia Mecânica', '8º', 2),
('Lucas Silva', 'Engenharia Civil', '2º', 2),
('Fernanda Alves', 'Engenharia Elétrica', '5º', 2),
('Rafael Souza', 'Engenharia Química', '9º', 2),
-- UNITRI
('Eduarda Silva', 'Engenharia Ambiental', '4º', 3),
('Felipe Andrade', 'Engenharia Civil', '3º', 3),
('Patrícia Gomes', 'Engenharia de Computação', '7º', 3),
('Vinícius Lima', 'Engenharia Mecânica', '8º', 3),
('Amanda Rocha', 'Engenharia de Produção', '5º', 3),
-- FEAR
('Gabriela Torres', 'Engenharia de Computação', '2º', 4),
('Henrique Alves', 'Engenharia Química', '9º', 4),
('Larissa Mendes', 'Engenharia Civil', '6º', 4),
('Thiago Pereira', 'Engenharia Elétrica', '4º', 4),
('Beatriz Ramos', 'Engenharia Ambiental', '5º', 4); 