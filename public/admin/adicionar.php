<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/conexao.php';
// TEMPORÁRIO: Autenticação desabilitada
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit;
}

$erro = '';
$sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = trim($_POST['texto'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $alternativas = $_POST['alternativas'] ?? [];
    $correta = $_POST['correta'] ?? '';
    if ($texto && in_array($categoria, ['facil','medio','dificil']) && count($alternativas) === 4 && in_array($correta, ['A','B','C','D'])) {
        $conn = getDbConnection();
        $stmt = $conn->prepare('INSERT INTO perguntas (texto, categoria) VALUES (?, ?)');
        $stmt->bind_param('ss', $texto, $categoria);
        if ($stmt->execute()) {
            $pergunta_id = $stmt->insert_id;
            $stmt->close();
            $letras = ['A','B','C','D'];
            $stmt2 = $conn->prepare('INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES (?, ?, ?, ?)');
            foreach ($letras as $i => $letra) {
                $alt_texto = trim($alternativas[$letra] ?? '');
                $is_correta = ($correta === $letra) ? 1 : 0;
                $stmt2->bind_param('issi', $pergunta_id, $letra, $alt_texto, $is_correta);
                $stmt2->execute();
            }
            $stmt2->close();
            $sucesso = 'Pergunta adicionada com sucesso!';
        } else {
            $erro = 'Erro ao adicionar pergunta.';
        }
        $conn->close();
    } else {
        $erro = 'Preencha todos os campos corretamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Pergunta - Admin Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; }
        .container { max-width: 700px; margin: 40px auto; background: #222; padding: 32px; border-radius: 12px; box-shadow: 0 0 24px #0008; }
        h2 { margin-top: 0; font-size: 1.3em; }
        label, legend { color: #eee; font-size: 1.05em; }
        input, textarea, select { width: 100%; margin-bottom: 16px; padding: 8px; border-radius: 4px; border: none; background: #181a1b; color: #eee; font-size: 1em; }
        textarea { resize: vertical; }
        button { padding: 10px 24px; background: #0ff; color: #222; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        button:hover { background: #3fffd6; }
        .erro { color: #f66; margin-bottom: 12px; }
        .sucesso { color: #0f6; margin-bottom: 12px; }
        fieldset {
            border: 1px solid #3fffd6;
            border-radius: 8px;
            padding: 16px 12px 12px 12px;
            margin-bottom: 16px;
            background: #23272a;
        }
        .alt-label {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            gap: 0;
        }
        .alt-label input[type="radio"] {
            margin-right: 18px;
            accent-color: #0ff;
            margin-left: 8px;
            width: 40px;
            height: 40px;
        }
        .alt-label strong {
            margin-left: 0;
            margin-right: 18px;
            white-space: nowrap;
            font-size: 1.15em;
            font-weight: bold;
        }
        .alt-label input[type="text"] {
            flex: 1;
            margin-left: 0;
            min-width: 200px;
            max-width: 100%;
            box-sizing: border-box;
            background: #181a1b;
            color: #eee;
            font-size: 1.08em;
            padding-left: 12px;
        }
        .botoes { display: flex; gap: 16px; margin-top: 18px; }
        .botoes button { background: #0ff; color: #222; }
        .botoes a { color: #0ff; text-decoration: none; padding: 10px 24px; border-radius: 4px; background: #181a1b; font-weight: bold; transition: 0.2s; }
        .botoes a:hover { background: #222; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 style="margin:0;font-size:1.2em;">Adicionar Nova Pergunta</h2>
            <a href="revisar.php" style="background:#0ff;color:#222;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:bold;">Revisar Perguntas</a>
        </div>
        <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="sucesso"><?= $sucesso ?></div><?php endif; ?>
        <form method="post">
            <label>Pergunta:<br>
                <textarea name="texto" required rows="3"></textarea>
            </label>
            <label>Categoria:
                <select name="categoria" required>
                    <option value="">Selecione</option>
                    <option value="facil">Fácil</option>
                    <option value="medio">Médio</option>
                    <option value="dificil">Difícil</option>
                </select>
            </label>
            <fieldset>
                <legend>Alternativas</legend>
                <?php foreach(['A','B','C','D'] as $letra): ?>
                <div class="alt-label">
                    <input type="radio" name="correta" value="<?= $letra ?>" required>
                    <strong><?= $letra ?>)</strong>
                    <input type="text" name="alternativas[<?= $letra ?>]" placeholder="Texto da alternativa <?= $letra ?>" required>
                </div>
                <?php endforeach; ?>
            </fieldset>
            <div class="botoes">
                <button type="submit">Salvar</button>
                <a href="revisar.php">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html> 