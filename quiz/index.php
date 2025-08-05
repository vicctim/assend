<?php
session_start();
require_once '../config.php';
require_once '../db/conexao.php';

$aluno = null;
$instituicao_nome = '';
$aluno_id = null;

if (isset($_GET['aluno_id'])) {
    $aluno_id = intval($_GET['aluno_id']);
    $alunos_sorteados = isset($_SESSION['alunos_sorteados']) ? $_SESSION['alunos_sorteados'] : [];
    $instituicoes_sorteadas = isset($_SESSION['instituicoes_sorteadas']) ? $_SESSION['instituicoes_sorteadas'] : [];
    foreach ($alunos_sorteados as $inst_id => $alunos) {
        foreach ($alunos as $a) {
            if ($a['id'] == $aluno_id) {
                $aluno = $a;
                // Buscar nome da instituição
                foreach ($instituicoes_sorteadas as $inst) {
                    if ($inst['id'] == $inst_id) {
                        $instituicao_nome = $inst['nome'];
                        break;
                    }
                }
                break 2;
            }
        }
    }
}

// Estado do quiz isolado por aluno
if ($aluno_id) {
    if (!isset($_SESSION['quiz'])) $_SESSION['quiz'] = [];
    if (!isset($_SESSION['quiz'][$aluno_id])) {
        $_SESSION['quiz'][$aluno_id] = [
            'categoria' => 'facil',
            'respondidas' => [],
            'opcoes_usadas' => ['placas'=>false,'convidados'=>false,'pular'=>false],
            'acertos' => ['facil'=>0,'medio'=>0,'dificil'=>0],
            'pulada' => false
        ];
    }
    $quiz = &$_SESSION['quiz'][$aluno_id];
} else {
    $quiz = [
        'categoria' => 'facil',
        'respondidas' => [],
        'opcoes_usadas' => ['placas'=>false,'convidados'=>false,'pular'=>false],
        'acertos' => ['facil'=>0,'medio'=>0,'dificil'=>0],
        'pulada' => false
    ];
}

$categoria = $quiz['categoria'];
$respondidas = $quiz['respondidas'];
$opcoes_usadas = $quiz['opcoes_usadas'];
$pulada = $quiz['pulada'];
$acertos = $quiz['acertos'];

// Categoria e layout
if ($categoria === 'medio') {
    $img_dir = '../img/medio';
    $background_img = "$img_dir/telavazia-medio.jpg";
    $body_class = 'quiz-medio';
} elseif ($categoria === 'dificil') {
    $img_dir = '../img/dificil';
    $background_img = "$img_dir/telavazia-dificil.jpg";
    $body_class = 'quiz-dificil';
} else {
    $img_dir = '../img/facil';
    $background_img = "$img_dir/telavazia-facil2.jpg";
    $body_class = 'quiz-facil';
}

// Buscar próxima pergunta não respondida
$pergunta = null;
if ($categoria) {
    $conn = getDbConnection();
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
    $conn->close();
}
// Buscar alternativas
$alternativas = [];
if ($pergunta) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT * FROM alternativas WHERE pergunta_id = ? ORDER BY letra');
    $stmt->bind_param('i', $pergunta['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($alt = $result->fetch_assoc()) {
        $alternativas[$alt['letra']] = $alt;
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quiz - Assend Show</title>
    <link rel="stylesheet" href="../assets/css/quiz.css?v=<?= time() ?>">
</head>
<body class="<?= $body_class ?>" style="background:url('<?= $background_img ?>') no-repeat center center;">
<div class="quiz-container">
    <?php if (!$pergunta): ?>
        <div class="pergunta">Quiz finalizado! Obrigado por participar.</div>
        <a href="../index.php">Menu Inicial</a>
    <?php else: ?>
        <div class="pergunta" id="pergunta-texto">
            <?php if ($categoria === 'medio'): ?>
                <img src="../img/medio/overlay-campo-pergunta-medio.png" class="pergunta-bg" alt="">
            <?php elseif ($categoria === 'dificil'): ?>
                <img src="../img/dificil/overlay-campo-pergunta-dificil.png" class="pergunta-bg" alt="">
            <?php else: ?>
                <img src="../img/facil/overlay-campo-pergunta.png" class="pergunta-bg" alt="">
            <?php endif; ?>
            <span class="pergunta-texto"><?= htmlspecialchars($pergunta['texto']) ?></span>
        </div>
        <div class="alternativas">
            <?php foreach ($alternativas as $letra => $alt): ?>
                <button class="alternativa-btn" data-letra="<?= $letra ?>" id="alt-<?= $letra ?>">
                    <span class="alt-letra"><?= $letra ?>)</span>
                    <span class="alt-texto"><?= htmlspecialchars($alt['texto']) ?></span>
                    <img src="../img/<?= $categoria ?>/overlay-alternativa-<?= strtolower($letra) ?>.png" class="overlay-img" id="overlay-<?= $letra ?>">
                </button>
            <?php endforeach; ?>
        </div>
        <div class="quiz-opcoes-extra">
            <?php
            $botoes = [
                'placas' => 'PLACAS',
                'convidados' => 'CONVIDADOS',
                'pular' => 'PULAR'
            ];
            foreach ($botoes as $key => $label):
                $classe = 'quiz-opcao-btn';
                if ($opcoes_usadas[$key]) $classe .= ' usado';
            ?>
                <button class="<?= $classe ?>" id="opcao-<?= $key ?>" data-opcao="<?= $key ?>" <?= $opcoes_usadas[$key] ? 'disabled' : '' ?>><?= $label ?></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php if ($pergunta): ?>
<script>window.PERGUNTA_ID = <?= (int)$pergunta['id'] ?>; window.ALUNO_ID = <?= (int)$aluno_id ?>;</script>
<?php endif; ?>
<script src="../assets/js/quiz.js?v=<?= time() ?>"></script>
</body>
</html> 