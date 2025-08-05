<?php
session_start();
require_once '../config.php';
require_once __DIR__ . '/../db/conexao.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$conn = getDbConnection();
$erro = '';
$sucesso = '';

// Confirmação de exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $stmt = $conn->prepare('DELETE FROM perguntas WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = 'Erro ao excluir pergunta.';
    }
    $stmt->close();
}
// Carregar pergunta para exibir confirmação
$stmt = $conn->prepare('SELECT texto FROM perguntas WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($texto);
$stmt->fetch();
$stmt->close();
$conn->close();
if (!$texto) die('Pergunta não encontrada.');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Pergunta - Admin Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; }
        .container { max-width: 500px; margin: 60px auto; background: #333; padding: 32px; border-radius: 8px; text-align: center; }
        button { padding: 10px 24px; background: #f66; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 16px; }
        a { color: #0ff; text-decoration: none; }
        .erro { color: #f66; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excluir Pergunta</h2>
        <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
        <p>Tem certeza que deseja excluir a pergunta abaixo? Esta ação não pode ser desfeita.</p>
        <blockquote><?= htmlspecialchars($texto) ?></blockquote>
        <form method="post">
            <button type="submit" name="confirmar" value="1">Sim, excluir</button>
            <a href="dashboard.php">Cancelar</a>
        </form>
    </div>
</body>
</html> 