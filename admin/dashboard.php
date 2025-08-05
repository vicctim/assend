<?php
session_start();
require_once '../config.php';
require_once __DIR__ . '/../db/conexao.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$conn = getDbConnection();

// Filtros
$categoria = $_GET['categoria'] ?? '';
$status = $_GET['status'] ?? '';
$where = [];
$params = [];
$types = '';
if ($categoria) {
    $where[] = 'categoria = ?';
    $params[] = $categoria;
    $types .= 's';
}
if ($status) {
    $where[] = 'status = ?';
    $params[] = $status;
    $types .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT * FROM perguntas $whereSql ORDER BY criada_em DESC LIMIT 100";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$perguntas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; margin: 0; }
        .topbar { background: #222; padding: 16px; display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { margin: 0; font-size: 1.5em; }
        .topbar a { color: #0ff; text-decoration: none; margin-left: 16px; }
        .filtros { margin: 24px; }
        .filtros select, .filtros button { padding: 6px 12px; margin-right: 8px; }
        table { width: 96%; margin: 0 auto 32px auto; border-collapse: collapse; background: #333; }
        th, td { padding: 10px; border: 1px solid #444; text-align: left; }
        th { background: #222; }
        tr:nth-child(even) { background: #2a2d31; }
        .actions a { margin-right: 8px; color: #0ff; text-decoration: none; }
        .actions a.delete { color: #f66; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Administração do Quiz</h1>
        <div>
            <a href="adicionar.php">Adicionar Pergunta</a>
            <a href="importar.php">Importar em Massa</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>
    <div class="filtros">
        <form method="get">
            <label>Categoria:
                <select name="categoria">
                    <option value="">Todas</option>
                    <option value="facil"<?= $categoria==='facil'?' selected':'' ?>>Fácil</option>
                    <option value="medio"<?= $categoria==='medio'?' selected':'' ?>>Médio</option>
                    <option value="dificil"<?= $categoria==='dificil'?' selected':'' ?>>Difícil</option>
                </select>
            </label>
            <label>Status:
                <select name="status">
                    <option value="">Todos</option>
                    <option value="nao_respondida"<?= $status==='nao_respondida'?' selected':'' ?>>Não respondida</option>
                    <option value="respondida"<?= $status==='respondida'?' selected':'' ?>>Respondida</option>
                    <option value="pulada"<?= $status==='pulada'?' selected':'' ?>>Pulada</option>
                </select>
            </label>
            <button type="submit">Filtrar</button>
        </form>
    </div>
    <form method="post" id="form-excluir-multiplas" onsubmit="return confirm('Tem certeza que deseja excluir as perguntas selecionadas? Esta ação não pode ser desfeita!')">
        <button type="submit" name="excluir_multiplas" style="margin:0 0 16px 0;padding:10px 24px;background:#f66;color:#fff;border:none;border-radius:4px;font-weight:bold;cursor:pointer;">Excluir Selecionadas</button>
        <table>
            <tr>
                <th><input type="checkbox" id="check-todos" onclick="marcarTodos(this)"></th>
                <th>ID</th>
                <th>Pergunta</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Criada em</th>
                <th>Ações</th>
            </tr>
            <?php
            // Conexão para auditoria
            $conn2 = getDbConnection();
            foreach ($perguntas as $p):
                // Buscar status real na auditoria
                $stmt2 = $conn2->prepare("SELECT acao FROM auditoria WHERE pergunta_id = ? ORDER BY data_hora DESC LIMIT 1");
                $stmt2->bind_param('i', $p['id']);
                $stmt2->execute();
                $stmt2->bind_result($acao_auditoria);
                $stmt2->fetch();
                $stmt2->close();
                if ($acao_auditoria === 'resposta') {
                    $status_real = 'Respondida';
                } elseif ($acao_auditoria === 'pular') {
                    $status_real = 'Pulada';
                } else {
                    $status_real = 'Não respondida';
                }
            ?>
            <tr>
                <td><input type="checkbox" name="ids[]" value="<?= $p['id'] ?>"></td>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($p['texto'], 0, 60, '...')) ?></td>
                <td><?= ucfirst($p['categoria']) ?></td>
                <td><?= $status_real ?></td>
                <td><?= $p['criada_em'] ?></td>
                <td class="actions">
                    <a href="editar.php?id=<?= $p['id'] ?>">Editar</a>
                    <a href="excluir.php?id=<?= $p['id'] ?>" class="delete" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</a>
                    <a href="alternativas.php?id=<?= $p['id'] ?>">Alternativas</a>
                </td>
            </tr>
            <?php endforeach; $conn2->close(); ?>
        </table>
    </form>
    <script>
    function marcarTodos(box) {
        const checks = document.querySelectorAll('input[name="ids[]"]');
        checks.forEach(c => c.checked = box.checked);
    }
    </script>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_multiplas']) && !empty($_POST['ids'])) {
    $conn = getDbConnection();
    $ids = array_map('intval', $_POST['ids']);
    $in = implode(',', $ids);
    $conn->query("DELETE FROM perguntas WHERE id IN ($in)");
    $conn->close();
    echo '<script>window.location.href = "dashboard.php";</script>';
    exit;
}
?> 