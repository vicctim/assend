<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/conexao.php';

// TEMPORÁRIO: Acesso sem autenticação
// Simula login automático
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_usuario'] = 'admin_temp';

// Redireciona diretamente para o dashboard
header('Location: dashboard.php');
exit;
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