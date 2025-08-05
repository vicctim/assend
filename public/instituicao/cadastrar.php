<?php
// inclui header e conexão
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../db/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $logo = $_FILES['logo'] ?? null;
    if ($nome && $logo && $logo['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $nome_arquivo = uniqid('logo_', true) . '.' . $ext;
            $destino = '../img/instituicoes/' . $nome_arquivo;
            if (move_uploaded_file($logo['tmp_name'], $destino)) {
                $caminho_logo = 'img/instituicoes/' . $nome_arquivo;
                $conn = getDbConnection();
                $stmt = $conn->prepare('INSERT INTO instituicoes (nome, logo) VALUES (?, ?)');
                $stmt->bind_param('ss', $nome, $caminho_logo);
                if ($stmt->execute()) {
                    $mensagem = '<span style="color:green">Instituição cadastrada com sucesso!</span>';
                } else {
                    $mensagem = '<span style="color:red">Erro ao salvar no banco de dados.</span>';
                }
                $stmt->close();
                $conn->close();
            } else {
                $mensagem = '<span style="color:red">Erro ao salvar o arquivo de logo.</span>';
            }
        } else {
            $mensagem = '<span style="color:red">Formato de logo inválido. Use PNG ou JPG.</span>';
        }
    } else {
        $mensagem = '<span style="color:red">Preencha todos os campos corretamente.</span>';
    }
}
?>
<h2>Cadastro de Instituição</h2>
<?php if ($mensagem) echo $mensagem; ?>
<form method="post" enctype="multipart/form-data">
    <label>Nome da Instituição:<br><input type="text" name="nome" required></label><br><br>
    <label>Logo (PNG/JPG):<br><input type="file" name="logo" accept="image/png, image/jpeg" required></label><br><br>
    <button type="submit">Cadastrar</button>
</form>
<br>
<a href="../aluno/cadastrar.php">Cadastrar Aluno</a> |
<a href="../index.php">Menu Inicial</a>
<?php
require_once __DIR__ . '/../includes/footer.php'; 