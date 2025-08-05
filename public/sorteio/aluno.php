<?php
session_start();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../db/conexao.php';

$mensagem = '';
$alunos = [];
$instituicao_id = $_GET['instituicao_id'] ?? null;

if ($instituicao_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT a.*, i.nome as instituicao_nome, i.logo as instituicao_logo 
                           FROM alunos a 
                           JOIN instituicoes i ON a.instituicao_id = i.id 
                           WHERE a.instituicao_id = ?');
    $stmt->bind_param('i', $instituicao_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sortear'])) {
    if ($alunos) {
        $aluno_sorteado = $alunos[array_rand($alunos)];
        $_SESSION['aluno_sorteado'] = $aluno_sorteado;
    } else {
        $mensagem = '<span style="color:red">Nenhum aluno cadastrado para esta instituição.</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sorteio de Aluno - Assend Show</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #0b1e2d;
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            color: #2ff0e6;
            overflow: hidden;
        }

        .container {
            width: 1920px;
            height: 1080px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .instituicao-info {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 30px;
            box-shadow: 0 0 30px rgba(47, 240, 230, 0.2);
        }

        .instituicao-info img {
            width: 120px;
            height: 120px;
            object-fit: contain;
        }

        .instituicao-info h2 {
            margin: 0;
            color: #2ff0e6;
            font-size: 2.5em;
            text-shadow: 0 0 20px rgba(47, 240, 230, 0.5);
        }

        .btn-sortear {
            position: absolute;
            bottom: 200px;
            left: 50%;
            transform: translateX(-50%);
            background: #2ff0e6;
            color: #0b1e2d;
            border: none;
            padding: 25px 80px;
            font-size: 2em;
            border-radius: 50px;
            cursor: pointer;
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            font-weight: bold;
            letter-spacing: 4px;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
            box-shadow: 0 0 40px rgba(47, 240, 230, 0.4);
        }

        .btn-sortear:hover {
            transform: translateX(-50%) scale(1.05);
            box-shadow: 0 0 60px rgba(47, 240, 230, 0.6);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(47, 240, 230, 0.6); }
            70% { box-shadow: 0 0 0 40px rgba(47, 240, 230, 0); }
            100% { box-shadow: 0 0 0 0 rgba(47, 240, 230, 0); }
        }

        .sorteio-animation {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(11, 30, 45, 0.95);
            z-index: 100;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .sorteio-animation.active {
            display: flex;
            opacity: 1;
        }

        .sorteio-content {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5em;
            color: #2ff0e6;
            text-shadow: 0 0 30px rgba(47, 240, 230, 0.7);
            font-weight: bold;
            letter-spacing: 8px;
        }

        .alunos-container {
            display: none;
            position: absolute;
            top: 200px;
            left: 50%;
            transform: translateX(-50%);
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            padding: 40px;
            max-width: 1600px;
        }

        .alunos-container.active {
            display: flex;
        }

        .aluno-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 0 40px rgba(127, 255, 218, 0.3);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .aluno-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 60px rgba(127, 255, 218, 0.5);
        }

        .aluno-card img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 25px;
            border: 5px solid #2ff0e6;
            box-shadow: 0 0 30px rgba(47, 240, 230, 0.4);
        }

        .aluno-card h3 {
            margin: 0;
            font-size: 2em;
            color: #2ff0e6;
            text-align: center;
            text-shadow: 0 0 20px rgba(47, 240, 230, 0.5);
        }

        .aluno-card p {
            margin: 10px 0;
            color: #fff;
            text-align: center;
            font-size: 1.4em;
        }

        .btn-iniciar-quiz {
            background: #2ff0e6;
            color: #0b1e2d;
            border: none;
            padding: 15px 40px;
            font-size: 1.4em;
            border-radius: 30px;
            cursor: pointer;
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            font-weight: bold;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            margin-top: 25px;
            text-decoration: none;
            box-shadow: 0 0 30px rgba(47, 240, 230, 0.4);
        }

        .btn-iniciar-quiz:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(47, 240, 230, 0.6);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($alunos && isset($alunos[0]['instituicao_nome'])): ?>
        <div class="instituicao-info">
            <img src="../<?= htmlspecialchars($alunos[0]['instituicao_logo'] ?? '') ?>" alt="">
            <h2><?= htmlspecialchars($alunos[0]['instituicao_nome']) ?></h2>
        </div>
        <?php endif; ?>

        <form method="post" id="sortearForm">
            <button type="submit" name="sortear" class="btn-sortear">SORTEAR ALUNO</button>
        </form>

        <?php if (isset($_SESSION['aluno_sorteado'])): ?>
        <div class="alunos-container active">
            <?php foreach ($alunos as $aluno): ?>
            <div class="aluno-card">
                <img src="../<?= htmlspecialchars($aluno['foto'] ?? '') ?>" alt="">
                <h3><?= htmlspecialchars($aluno['nome']) ?></h3>
                <p><?= htmlspecialchars($aluno['curso'] ?? '') ?></p>
                <p><?= htmlspecialchars($aluno['matricula'] ?? '') ?></p>
                <a href="../quiz/index.php?aluno_id=<?= $aluno['id'] ?>" class="btn-iniciar-quiz">INICIAR QUIZ</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="sorteio-animation">
        <div class="sorteio-content" id="sorteioContent"></div>
    </div>

    <script>
        document.getElementById('sortearForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostra a animação
            const sorteioAnimation = document.querySelector('.sorteio-animation');
            const sorteioContent = document.getElementById('sorteioContent');
            sorteioAnimation.classList.add('active');
            
            // Lista de alunos para a animação
            const alunos = <?= json_encode(array_column($alunos, 'nome')) ?>;
            let currentIndex = 0;
            let spinCount = 0;
            const maxSpins = 50;
            let spinInterval = 50;
            let slowDownPoint = 35;
            
            // Função para girar os nomes
            function spinNames() {
                sorteioContent.textContent = alunos[currentIndex];
                currentIndex = (currentIndex + 1) % alunos.length;
                spinCount++;
                
                if (spinCount > slowDownPoint) {
                    spinInterval += 20;
                }
                
                if (spinCount < maxSpins) {
                    setTimeout(spinNames, spinInterval);
                } else {
                    setTimeout(() => {
                        this.submit();
                    }, 2000);
                }
            }
            
            spinNames();
        });
    </script>
</body>
</html> 