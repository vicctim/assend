<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../db/conexao.php';
header('Content-Type: application/json; charset=UTF-8');

$input = json_decode(file_get_contents('php://input'), true);
$aluno_id = isset($input['aluno_id']) ? intval($input['aluno_id']) : (isset($_SESSION['aluno_sorteado']['id']) ? $_SESSION['aluno_sorteado']['id'] : null);
if (!$aluno_id) {
    echo json_encode(['erro' => 'Aluno não identificado.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!isset($_SESSION['quiz'][$aluno_id])) {
    // Inicializa se não existir
    $_SESSION['quiz'][$aluno_id] = [
        'categoria' => 'facil',
        'respondidas' => [],
        'opcoes_usadas' => ['placas'=>false,'convidados'=>false,'pular'=>false],
        'acertos' => ['facil'=>0,'medio'=>0,'dificil'=>0],
        'pulada' => false
    ];
}
$quiz = &$_SESSION['quiz'][$aluno_id];
$categoria = $quiz['categoria'];
$respondidas = &$quiz['respondidas'];
$opcoes_usadas = &$quiz['opcoes_usadas'];
$acertos = &$quiz['acertos'];
$pulada = &$quiz['pulada'];

$acao = $input['acao'] ?? '';

if ($acao === 'confirmar' && isset($input['letra']) && isset($input['pergunta_id'])) {
    $pergunta_id = (int)$input['pergunta_id'];
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT * FROM perguntas WHERE id = ?');
    $stmt->bind_param('i', $pergunta_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pergunta = $result->fetch_assoc();
    $stmt->close();
    if (!$pergunta) {
        $conn->close();
        echo json_encode(['erro' => 'Pergunta não encontrada']);
        exit;
    }
    $stmt = $conn->prepare('SELECT letra, correta FROM alternativas WHERE pergunta_id = ?');
    $stmt->bind_param('i', $pergunta['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $correta_letra = '';
    while ($alt = $result->fetch_assoc()) {
        if ($alt['correta']) $correta_letra = $alt['letra'];
    }
    $stmt->close();
    $resposta_dada = strtoupper($input['letra']);
    $resposta_correta = strtoupper($correta_letra);
    $respondidas[] = $pergunta['id'];
    // Registrar na auditoria
    $stmt = $conn->prepare('SELECT nome, curso, periodo, instituicao_id FROM alunos WHERE id = ?');
    $stmt->bind_param('i', $aluno_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();
    $stmt->close();
    // Buscar nome da instituição
    $instituicao_nome = '';
    if (!empty($aluno['instituicao_id'])) {
        $stmt = $conn->prepare('SELECT nome FROM instituicoes WHERE id = ?');
        $stmt->bind_param('i', $aluno['instituicao_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $inst = $result->fetch_assoc();
        $instituicao_nome = $inst ? $inst['nome'] : '';
        $stmt->close();
    }
    $acao_auditoria = 'resposta';
    $data_hora = date('Y-m-d H:i:s');
    $aluno_nome = $aluno['nome'];
    $stmt = $conn->prepare('INSERT INTO auditoria (acao, tabela, registro_id, usuario, dados_novos) VALUES (?, ?, ?, ?, ?)');
    $dados_json = json_encode([
        'aluno_nome' => $aluno_nome,
        'instituicao_nome' => $instituicao_nome,
        'resposta_dada' => $resposta_dada,
        'resposta_correta' => $resposta_correta,
        'aluno_id' => $aluno_id
    ]);
    $stmt->bind_param('ssiss', $acao_auditoria, 'perguntas', $pergunta['id'], $aluno_nome, $dados_json);
    $stmt->execute();
    $stmt->close();
    if ($resposta_dada === $resposta_correta) {
        $acertos[$categoria]++;
        // Troca de categoria se acertar 5
        if ($acertos[$categoria] >= 5) {
            if ($categoria === 'facil') {
                $quiz['categoria'] = 'medio';
                $quiz['respondidas'] = [];
            } elseif ($categoria === 'medio') {
                $quiz['categoria'] = 'dificil';
                $quiz['respondidas'] = [];
            } elseif ($categoria === 'dificil') {
                // Vencedor: salvar pontuação
                salvarPontuacaoRanking($aluno_id, $quiz);
                echo json_encode(['correta' => true, 'vencedor' => true]);
                exit;
            }
            $acertos[$categoria] = 0;
            salvarPontuacaoRanking($aluno_id, $quiz);
            echo json_encode(['correta' => true, 'proxima_categoria' => $quiz['categoria']]);
            exit;
        }
        salvarPontuacaoRanking($aluno_id, $quiz);
        echo json_encode(['correta' => true]);
    } else {
        // Se acabou as perguntas, salvar pontuação
        if (count($respondidas) >= 15) { // Exemplo: 15 perguntas
            salvarPontuacaoRanking($aluno_id, $quiz);
        } else {
            salvarPontuacaoRanking($aluno_id, $quiz);
        }
        echo json_encode(['correta' => false, 'correta_letra' => $correta_letra]);
    }
    exit;
}

if ($acao === 'pular' && !$pulada) {
    $pulada = true;
    echo json_encode(['pulado' => true]);
    exit;
}

if ($acao === 'opcao_extra' && isset($input['opcao'])) {
    $opcao = $input['opcao'];
    $opcoes_usadas[$opcao] = true;
    echo json_encode(['ok' => true, 'opcoes_usadas' => $opcoes_usadas]);
    exit;
}

if ($acao === 'nova') {
    $conn = getDbConnection();
    $respondidas = $input['respondidas'] ?? [];
    $categoria = $input['categoria'] ?? $_SESSION['quiz'][$aluno_id]['categoria'];
    $placeholders = implode(',', array_fill(0, count($respondidas), '?'));
    $sql = 'SELECT * FROM perguntas WHERE categoria = ?';
    if ($respondidas) {
        $sql .= ' AND id NOT IN (' . $placeholders . ')';
    }
    $sql .= ' ORDER BY RAND() LIMIT 1';
    $stmt = $conn->prepare($sql);
    $types = 's' . str_repeat('i', count($respondidas));
    $params = array_merge([$categoria], $respondidas);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $pergunta = $result->fetch_assoc();
    $stmt->close();
    $alternativas = [];
    if ($pergunta) {
        $stmt = $conn->prepare('SELECT letra, texto FROM alternativas WHERE pergunta_id = ? ORDER BY letra');
        $stmt->bind_param('i', $pergunta['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($alt = $result->fetch_assoc()) {
            $alternativas[$alt['letra']] = $alt['texto'];
        }
        $stmt->close();
    }
    $conn->close();
    echo json_encode([
        'pergunta' => $pergunta,
        'alternativas' => $alternativas
    ]);
    exit;
}

echo json_encode(['erro' => 'Ação inválida']);

function salvarPontuacaoRanking($aluno_id, $quiz) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT nome, curso, periodo, instituicao_id FROM alunos WHERE id = ?');
    $stmt->bind_param('i', $aluno_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();
    $stmt->close();

    // Buscar nome da instituição
    $instituicao_nome = '';
    if (!empty($aluno['instituicao_id'])) {
        $stmt = $conn->prepare('SELECT nome FROM instituicoes WHERE id = ?');
        $stmt->bind_param('i', $aluno['instituicao_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $inst = $result->fetch_assoc();
        $instituicao_nome = $inst ? $inst['nome'] : '';
        $stmt->close();
    }

    $pontuacao = ($quiz['acertos']['facil'] ?? 0) * 10 + ($quiz['acertos']['medio'] ?? 0) * 20 + ($quiz['acertos']['dificil'] ?? 0) * 50;
    // Salva ou atualiza no ranking
    $stmt = $conn->prepare('REPLACE INTO ranking (aluno_id, aluno_nome, curso, instituicao_nome, pontuacao) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('isssi', $aluno_id, $aluno['nome'], $aluno['curso'], $instituicao_nome, $pontuacao);
    $stmt->execute();
    $stmt->close();
    $conn->close();
} 