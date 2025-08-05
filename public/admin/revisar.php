<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/conexao.php';
// TEMPORÁRIO: Autenticação desabilitada
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit;
}

$conn = getDbConnection();

// Navegação sequencial
$perguntaAtual = isset($_GET['idx']) ? intval($_GET['idx']) : 0;
$categoria = $_GET['categoria'] ?? '';
$where = [];
$params = [];
$types = '';
if ($categoria) {
    $where[] = 'categoria = ?';
    $params[] = $categoria;
    $types .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT * FROM perguntas $whereSql ORDER BY id ASC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$perguntas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = count($perguntas);
$pergunta = $perguntas[$perguntaAtual] ?? null;
$alternativas = [];
$correta = '';
if ($pergunta) {
    $stmt = $conn->prepare('SELECT * FROM alternativas WHERE pergunta_id = ? ORDER BY letra');
    $stmt->bind_param('i', $pergunta['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($alt = $res->fetch_assoc()) {
        $alternativas[$alt['letra']] = $alt['texto'];
        if ($alt['correta']) $correta = $alt['letra'];
    }
    $stmt->close();
}
$conn->close();

// Marcação de revisadas (sessão)
if (!isset($_SESSION['revisadas'])) $_SESSION['revisadas'] = [];
$revisada = in_array($pergunta['id'], $_SESSION['revisadas'] ?? []);

// Salvar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correta'])) {
    $novaCorreta = $_POST['correta'];
    $conn = getDbConnection();
    $stmt = $conn->prepare('UPDATE alternativas SET correta=IF(letra=?,1,0) WHERE pergunta_id=?');
    $stmt->bind_param('si', $novaCorreta, $pergunta['id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    // Marca como revisada
    $_SESSION['revisadas'][] = $pergunta['id'];
    // Redireciona para próxima
    $prox = min($perguntaAtual+1, $total-1);
    header('Location: revisar.php?idx='.$prox.($categoria ? '&categoria='.$categoria : ''));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Revisão Rápida de Perguntas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; margin: 0; }
        .container { max-width: 700px; margin: 40px auto; background: #222; border-radius: 12px; box-shadow: 0 0 24px #0008; padding: 32px; }
        .pergunta-titulo { font-size: 1.3em; margin-bottom: 12px; }
        .pergunta-meta { color: #aaa; font-size: 0.95em; margin-bottom: 18px; }
        .alternativas { margin: 18px 0 24px 0; }
        .alt-row { margin-bottom: 10px; }
        .alt-radio { margin-right: 8px; }
        .revisada { border: 2px solid #3fffd6; box-shadow: 0 0 12px #3fffd6a0; }
        .botoes { display: flex; gap: 16px; margin-top: 24px; }
        .botoes button { padding: 8px 22px; font-size: 1.1em; border-radius: 6px; border: none; background: #3fffd6; color: #222; cursor: pointer; transition: 0.2s; }
        .botoes button:disabled { background: #444; color: #aaa; cursor: not-allowed; }
        .expandir { color: #3fffd6; cursor: pointer; text-decoration: underline; font-size: 1em; }
    </style>
    <script>
    function expandirAlternativas() {
        document.getElementById('alternativas').style.display = 'block';
        document.getElementById('expandir-link').style.display = 'none';
    }
    </script>
</head>
<body>
    <div class="container<?= $revisada ? ' revisada' : '' ?>">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 style="margin:0;font-size:1.2em;">Revisão Rápida de Perguntas</h2>
            <a href="adicionar.php" style="background:#0ff;color:#222;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:bold;">+ Adicionar Pergunta</a>
        </div>
        <?php if ($pergunta): ?>
            <div class="pergunta-titulo"><b>Pergunta <?= $perguntaAtual+1 ?>/<?= $total ?>:</b> <?= htmlspecialchars($pergunta['texto']) ?></div>
            <div class="pergunta-meta">
                Categoria: <b><?= ucfirst($pergunta['categoria']) ?></b> |
                Status: <b><?= ucfirst(str_replace('_',' ',$pergunta['status'])) ?></b> |
                Criada em: <?= $pergunta['criada_em'] ?>
            </div>
            <form method="post">
                <div class="alternativas" id="alternativas">
                    <?php foreach ($alternativas as $letra => $texto): ?>
                        <div class="alt-row">
                            <label>
                                <input type="radio" class="alt-radio" name="correta" value="<?= $letra ?>" <?= $correta===$letra?'checked':'' ?>>
                                <b<?= $correta===$letra?' style="color:#0f6;"':'' ?>><?= $letra ?>)</b>
                                <span<?= $correta===$letra?' style="color:#0f6; font-weight:bold;"':'' ?>><?= htmlspecialchars($texto) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="botoes">
                    <button type="button" onclick="window.location='revisar.php?idx=<?= max($perguntaAtual-1,0).($categoria ? '&categoria='.$categoria : '') ?>'" <?= $perguntaAtual==0?'disabled':'' ?>>Anterior</button>
                    <button type="submit">Salvar e Próxima</button>
                    <button type="button" style="background:#f66;color:#fff;" onclick="if(confirm('Tem certeza que deseja excluir esta pergunta?')) window.location='excluir.php?id=<?= $pergunta['id'] ?>';">Excluir</button>
                </div>
            </form>
        <?php else: ?>
            <div style="color:#f66;font-size:1.2em;">Nenhuma pergunta encontrada.</div>
        <?php endif; ?>
    </div>
</body>
</html> 