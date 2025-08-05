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
$aluno = $_GET['aluno'] ?? '';
$pergunta_id = $_GET['pergunta_id'] ?? '';
$acao = $_GET['acao'] ?? '';
$data = $_GET['data'] ?? '';
$where = [];
$params = [];
$types = '';
if ($aluno) {
    $where[] = 'aluno_nome LIKE ?';
    $params[] = "%$aluno%";
    $types .= 's';
}
if ($pergunta_id) {
    $where[] = 'pergunta_id = ?';
    $params[] = $pergunta_id;
    $types .= 'i';
}
if ($acao) {
    $where[] = 'acao = ?';
    $params[] = $acao;
    $types .= 's';
}
if ($data) {
    $where[] = 'DATE(data_hora) = ?';
    $params[] = $data;
    $types .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT * FROM auditoria $whereSql ORDER BY data_hora DESC LIMIT 200";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$registros = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Auditoria - Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; margin: 0; }
        .topbar { background: #222; padding: 16px; display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { margin: 0; font-size: 1.5em; }
        .topbar a { color: #0ff; text-decoration: none; margin-left: 16px; }
        .filtros { margin: 24px; }
        .filtros input, .filtros select, .filtros button { padding: 6px 12px; margin-right: 8px; }
        table { width: 98%; margin: 0 auto 32px auto; border-collapse: collapse; background: #333; }
        th, td { padding: 10px; border: 1px solid #444; text-align: left; }
        th { background: #222; }
        tr:nth-child(even) { background: #2a2d31; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Painel de Auditoria</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="adicionar.php">Adicionar Pergunta</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>
    <div class="filtros">
        <form method="get">
            <input type="text" name="aluno" placeholder="Aluno" value="<?= htmlspecialchars($aluno) ?>">
            <input type="number" name="pergunta_id" placeholder="Pergunta ID" value="<?= htmlspecialchars($pergunta_id) ?>" style="width:110px;">
            <select name="acao">
                <option value="">Todas ações</option>
                <option value="resposta"<?= $acao==='resposta'?' selected':'' ?>>Resposta</option>
                <option value="pular"<?= $acao==='pular'?' selected':'' ?>>Pular</option>
            </select>
            <input type="date" name="data" value="<?= htmlspecialchars($data) ?>">
            <button type="submit">Filtrar</button>
        </form>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Data/Hora</th>
            <th>Ação</th>
            <th>Aluno</th>
            <th>Instituição</th>
            <th>Pergunta ID</th>
            <th>Resposta Dada</th>
            <th>Resposta Correta</th>
            <th>Aluno ID</th>
        </tr>
        <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['data_hora'] ?></td>
            <td><?= ucfirst($r['acao']) ?></td>
            <td><?= htmlspecialchars($r['aluno_nome']) ?></td>
            <td><?= htmlspecialchars($r['instituicao_nome']) ?></td>
            <td><?= $r['pergunta_id'] ?></td>
            <td><?= $r['resposta_dada'] ?></td>
            <td><?= $r['resposta_correta'] ?></td>
            <td><?= $r['aluno_id'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html> 