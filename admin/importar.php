<?php
session_start();
require_once '../config.php';
require_once __DIR__ . '/../db/conexao.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$erro = '';
$sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['jsonfile'])) {
    $file = $_FILES['jsonfile'];
    if ($file['error'] === UPLOAD_ERR_OK && pathinfo($file['name'], PATHINFO_EXTENSION) === 'json') {
        $json = file_get_contents($file['tmp_name']);
        $data = json_decode($json, true);
        if (!$data || !isset($data['questoes'])) {
            $erro = 'Arquivo JSON inválido ou estrutura incorreta.';
        } else {
            $conn = getDbConnection();
            $importadas = 0;
            foreach ($data['questoes'] as $q) {
                $enunciado = trim($q['enunciado'] ?? '');
                $alternativas = $q['alternativas'] ?? [];
                $correta = strtoupper(trim($q['resposta_correta'] ?? ''));
                $categoria = strtolower(trim($q['categoria'] ?? ''));
                if (!$enunciado || count($alternativas) !== 4 || !in_array($correta, ['A','B','C','D']) || !in_array($categoria, ['facil','medio','dificil'])) continue;
                $stmt = $conn->prepare('INSERT INTO perguntas (texto, categoria) VALUES (?, ?)');
                $stmt->bind_param('ss', $enunciado, $categoria);
                if ($stmt->execute()) {
                    $pergunta_id = $stmt->insert_id;
                    $stmt->close();
                    foreach(['A','B','C','D'] as $letra) {
                        $alt_texto = trim($alternativas[strtolower($letra)] ?? '');
                        $is_correta = ($correta === $letra) ? 1 : 0;
                        $stmt2 = $conn->prepare('INSERT INTO alternativas (pergunta_id, letra, texto, correta) VALUES (?, ?, ?, ?)');
                        $stmt2->bind_param('issi', $pergunta_id, $letra, $alt_texto, $is_correta);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                    $importadas++;
                } else {
                    $stmt->close();
                }
            }
            $conn->close();
            $sucesso = "$importadas perguntas importadas com sucesso!";
        }
    } else {
        $erro = 'Selecione um arquivo JSON válido.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Importar Perguntas via JSON - Admin Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; }
        .container { max-width: 700px; margin: 40px auto; background: #333; padding: 32px; border-radius: 8px; }
        input[type=file] { margin-bottom: 16px; }
        button { padding: 10px 24px; background: #0ff; color: #222; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .erro { color: #f66; margin-bottom: 12px; }
        .sucesso { color: #0f6; margin-bottom: 12px; }
        .exemplo { background: #222; color: #aaa; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 0.95em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Importar Perguntas via JSON</h2>
        <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="sucesso"><?= $sucesso ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Selecione o arquivo JSON:
                <input type="file" name="jsonfile" accept="application/json" required>
            </label>
            <button type="submit">Importar</button>
            <a href="dashboard.php" style="margin-left:16px;color:#0ff;">Voltar</a>
        </form>
        <div class="exemplo">
            <strong>Exemplo de estrutura JSON:</strong><br>
<pre style="color:#aaa; background:#181818; padding:8px; border-radius:4px; font-size:0.95em; overflow-x:auto;">
{
  "questoes": [
    {
      "enunciado": "Qual é a principal função do CREA?",
      "alternativas": {
        "a": "Emitir licenças ambientais",
        "b": "Fiscalizar o exercício profissional da engenharia",
        "c": "Emitir CPF",
        "d": "Organizar concursos públicos"
      },
      "resposta_correta": "B",
      "categoria": "facil"
    }
  ]
}
</pre>
        </div>
    </div>
</body>
</html> 