<?php
require_once '../includes/header.php';
require_once '../db/conexao.php';

$mensagem = '';
// Buscar instituições cadastradas do banco
$conn = getDbConnection();
$instituicoes = [];
$res = $conn->query('SELECT id, nome FROM instituicoes ORDER BY nome');
while ($row = $res->fetch_assoc()) {
    $instituicoes[] = $row;
}
$res->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $instituicao_id = intval($_POST['instituicao_id'] ?? 0);
    if ($nome && $curso && $instituicao_id) {
        $stmt = $conn->prepare('INSERT INTO alunos (nome, curso, periodo, instituicao_id) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $nome, $curso, $periodo, $instituicao_id);
        if ($stmt->execute()) {
            $mensagem = '<span style="color:green">Aluno cadastrado com sucesso!</span>';
        } else {
            $mensagem = '<span style="color:red">Erro ao salvar no banco de dados.</span>';
        }
        $stmt->close();
    } else {
        $mensagem = '<span style="color:red">Preencha todos os campos obrigatórios.</span>';
    }
}
$conn->close();
?>
<h2>Cadastro de Aluno</h2>
<?php if ($mensagem) echo $mensagem; ?>
<form method="post">
    <label>Nome do Aluno:<br><input type="text" name="nome" required></label><br><br>
    <label>Curso:<br><input type="text" name="curso" required></label><br><br>
    <label>Período:<br><input type="text" name="periodo"></label><br><br>
    <label>Instituição:<br>
        <select name="instituicao_id" required>
            <option value="">Selecione</option>
            <?php foreach ($instituicoes as $inst): ?>
                <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>
    <button type="submit">Cadastrar</button>
</form>
<br>
<a href="../sorteio/instituicao.php">Sortear Instituição</a> |
<a href="../index.php">Menu Inicial</a>
<?php
require_once '../includes/footer.php'; 