<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/conexao.php';
// TEMPORÁRIO: Autenticação desabilitada
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit;
}

$id = intval($_GET['id'] ?? 0);
$conn = getDbConnection();
$erro = '';
$sucesso = '';

// Carregar pergunta e alternativas
$stmt = $conn->prepare('SELECT * FROM perguntas WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$pergunta = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$pergunta) {
    $conn->close();
    die('Pergunta não encontrada.');
}
$stmt = $conn->prepare('SELECT * FROM alternativas WHERE pergunta_id = ? ORDER BY letra');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$alternativas = [];
$correta = '';
while ($alt = $res->fetch_assoc()) {
    $alternativas[$alt['letra']] = $alt['texto'];
    if ($alt['correta']) $correta = $alt['letra'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = trim($_POST['texto'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $alts = $_POST['alternativas'] ?? [];
    $nova_correta = $_POST['correta'] ?? '';
    if ($texto && in_array($categoria, ['facil','medio','dificil']) && count($alts) === 4 && in_array($nova_correta, ['A','B','C','D'])) {
        $stmt = $conn->prepare('UPDATE perguntas SET texto=?, categoria=? WHERE id=?');
        $stmt->bind_param('ssi', $texto, $categoria, $id);
        $stmt->execute();
        $stmt->close();
        foreach(['A','B','C','D'] as $letra) {
            $alt_texto = trim($alts[$letra] ?? '');
            $is_correta = ($nova_correta === $letra) ? 1 : 0;
            $stmt = $conn->prepare('UPDATE alternativas SET texto=?, correta=? WHERE pergunta_id=? AND letra=?');
            $stmt->bind_param('siis', $alt_texto, $is_correta, $id, $letra);
            $stmt->execute();
            $stmt->close();
        }
        $sucesso = 'Pergunta atualizada com sucesso!';
        $correta = $nova_correta;
        $alternativas = $alts;
        $pergunta['texto'] = $texto;
        $pergunta['categoria'] = $categoria;
    } else {
        $erro = 'Preencha todos os campos corretamente.';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pergunta - Admin Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; }
        .container { max-width: 600px; margin: 40px auto; background: #333; padding: 32px; border-radius: 8px; }
        input, textarea, select { width: 100%; margin-bottom: 16px; padding: 8px; border-radius: 4px; border: none; }
        button { padding: 10px 24px; background: #0ff; color: #222; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .erro { color: #f66; margin-bottom: 12px; }
        .sucesso { color: #0f6; margin-bottom: 12px; }
        fieldset {
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 12px 8px 8px 8px;
            margin-bottom: 16px;
        }
        .alt-label {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .alt-label input[type="radio"] {
            margin-right: 8px;
        }
        .alt-label strong {
            margin-left: 8px;
            margin-right: 8px;
            white-space: nowrap;
        }
        .alt-label input[type="text"] {
            flex: 1;
            margin-left: 8px;
            min-width: 200px;
            max-width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Pergunta</h2>
        <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="sucesso"><?= $sucesso ?></div><?php endif; ?>
        <form method="post">
            <label>Pergunta:<br>
                <textarea name="texto" required rows="3"><?= htmlspecialchars($pergunta['texto']) ?></textarea>
            </label>
            <label>Categoria:
                <select name="categoria" required>
                    <option value="facil"<?= $pergunta['categoria']==='facil'?' selected':'' ?>>Fácil</option>
                    <option value="medio"<?= $pergunta['categoria']==='medio'?' selected':'' ?>>Médio</option>
                    <option value="dificil"<?= $pergunta['categoria']==='dificil'?' selected':'' ?>>Difícil</option>
                </select>
            </label>
            <fieldset>
                <legend>Alternativas</legend>
                <?php foreach(['A','B','C','D'] as $letra): ?>
                <div class="alt-label">
                    <input type="radio" name="correta" value="<?= $letra ?>" required<?= $correta===$letra?' checked':'' ?>>
                    <strong><?= $letra ?>)</strong>
                    <input type="text" name="alternativas[<?= $letra ?>]" value="<?= htmlspecialchars($alternativas[$letra] ?? '') ?>" required>
                </div>
                <?php endforeach; ?>
            </fieldset>
            <button type="submit">Salvar</button>
            <a href="dashboard.php" style="margin-left:16px;color:#0ff;">Voltar</a>
        </form>
    </div>
</body>
</html> 