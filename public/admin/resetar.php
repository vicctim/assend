<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/conexao.php';
// TEMPORÁRIO: Autenticação desabilitada
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit;
}

// LOG DE DEPURAÇÃO
error_log('RESET: Página acessada por ' . (isset($_SESSION['admin_logged_in']) ? 'admin' : 'visitante'));

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('RESET: POST recebido. Dados: ' . print_r($_POST, true));
    if (isset($_POST['reset_auditoria'])) {
        $conn = getDbConnection();
        $conn->query('TRUNCATE TABLE auditoria');
        $conn->close();
        $msg = 'Auditoria resetada com sucesso!';
    }
    if (isset($_POST['reset_alunos'])) {
        $conn = getDbConnection();
        $conn->query('DELETE FROM alunos');
        $conn->query('ALTER TABLE alunos AUTO_INCREMENT = 1');
        $conn->close();
        $msg = 'Alunos resetados!';
    }
    if (isset($_POST['reset_instituicoes'])) {
        $conn = getDbConnection();
        $conn->query('DELETE FROM instituicoes');
        $conn->query('ALTER TABLE instituicoes AUTO_INCREMENT = 1');
        $conn->close();
        $msg = 'Instituições resetadas!';
    }
    if (isset($_POST['reset_sessao'])) {
        unset(
            $_SESSION['quiz_categoria'],
            $_SESSION['quiz_respondidas'],
            $_SESSION['quiz_pulada'],
            $_SESSION['quiz_acertos'],
            $_SESSION['quiz_opcoes_usadas'],
            $_SESSION['aluno_sorteado'],
            $_SESSION['instituicao_sorteada'],
            $_SESSION['revisadas']
        );
        $msg = 'Sessão do quiz resetada!';
    }
    if (isset($_POST['reset_sorteio'])) {
        unset(
            $_SESSION['aluno_sorteado'],
            $_SESSION['instituicao_sorteada'],
            $_SESSION['participantes_anteriores'],
            $_SESSION['ultimo_participante_nome']
        );
        $msg = 'Sorteios resetados!';
    }
    if (isset($_POST['reset_sorteios_banco'])) {
        $conn = getDbConnection();
        $conn->query('DELETE FROM auditoria');
        $conn->query('ALTER TABLE auditoria AUTO_INCREMENT = 1');
        $conn->query('DELETE FROM sorteios');
        $conn->query('ALTER TABLE sorteios AUTO_INCREMENT = 1');
        $conn->close();
        $msg = 'Tabela de sorteios e auditoria resetadas!';
    }
    if (isset($_POST['reset_ranking'])) {
        $conn = getDbConnection();
        $conn->query('TRUNCATE TABLE ranking');
        $conn->close();
        $msg = 'Ranking resetado com sucesso!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Reset - Admin Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #23272a; color: #eee; }
        .container { max-width: 500px; margin: 60px auto; background: #222; padding: 32px; border-radius: 12px; box-shadow: 0 0 24px #0008; }
        h2 { margin-top: 0; }
        form { margin-bottom: 24px; }
        button { padding: 10px 24px; background: #0ff; color: #222; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 16px; transition: 0.2s; }
        button.reset-auditoria { background: #f66; color: #fff; }
        button.reset-sessao { background: #3fffd6; color: #222; }
        button.reset-sorteio { background: #0ff; color: #222; }
        .msg { color: #0f6; margin-bottom: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Painel de Reset de Testes</h2>
        <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
        <form method="post">
            <button type="submit" name="reset_auditoria" class="reset-auditoria">Resetar Auditoria</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_alunos" class="reset-sorteio" style="background:#f66;color:#fff;">Resetar Alunos</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_instituicoes" class="reset-sorteio" style="background:#f66;color:#fff;">Resetar Instituições</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_sessao" class="reset-sessao">Resetar Sessão do Quiz</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_sorteio" class="reset-sorteio">Resetar Sorteios</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_sorteios_banco" class="reset-sorteio" style="background:#f66;color:#fff;">Resetar Sorteios (Banco)</button>
        </form>
        <form method="post">
            <button type="submit" name="reset_ranking" class="reset-sorteio" style="background:#0af;color:#fff;">Resetar Ranking</button>
        </form>
        <a href="dashboard.php" style="color:#0ff;">Voltar ao Dashboard</a>
    </div>
</body>
</html> 