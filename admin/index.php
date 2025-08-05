<?php
session_start();
require_once '../config.php';
require_once __DIR__ . '/../db/conexao.php';

// Redireciona se já estiver logado
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT senha FROM admin WHERE usuario = ?');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $stmt->bind_result($senha_hash);
    if ($stmt->fetch() && password_verify($senha, $senha_hash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_usuario'] = $usuario;
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = 'Usuário ou senha inválidos.';
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #222; color: #eee; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-box { background: #333; padding: 32px 24px; border-radius: 8px; box-shadow: 0 0 12px #0008; }
        input { display: block; width: 100%; margin-bottom: 16px; padding: 8px; border-radius: 4px; border: none; }
        button { width: 100%; padding: 10px; background: #0ff; color: #222; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .erro { color: #f66; margin-bottom: 12px; }
    </style>
</head>
<body>
    <form class="login-box" method="post">
        <h2>Login Admin</h2>
        <?php if ($erro): ?><div class="erro"><?= $erro ?></div><?php endif; ?>
        <input type="text" name="usuario" placeholder="Usuário" required autofocus>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html> 