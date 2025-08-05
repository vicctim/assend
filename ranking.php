<?php
require_once 'config.php';
require_once __DIR__ . '/db/conexao.php';
$conn = getDbConnection();
$sql = "SELECT 
    a.aluno_id, 
    a.aluno_nome, 
    a.instituicao_nome, 
    al.curso, 
    SUM(CASE WHEN a.resposta_dada = a.resposta_correta THEN CASE WHEN p.categoria = 'facil' THEN 10 WHEN p.categoria = 'medio' THEN 20 WHEN p.categoria = 'dificil' THEN 50 ELSE 0 END ELSE 0 END) AS pontuacao
FROM auditoria a
JOIN alunos al ON al.id = a.aluno_id
JOIN perguntas p ON p.id = a.pergunta_id
WHERE a.acao = 'resposta'
GROUP BY a.aluno_id, a.aluno_nome, a.instituicao_nome, al.curso
ORDER BY pontuacao DESC, a.aluno_nome ASC
LIMIT 50";
$res = $conn->query($sql);
$ranking = [];
while ($row = $res->fetch_assoc()) {
    $ranking[] = $row;
}
$res->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ranking - Quiz</title>
    <link rel="stylesheet" href="assets/css/quiz.css?v=<?= time() ?>">
    <!--<meta http-equiv="refresh" content="10">-->
    <style>
        html, body {
            width: 100vw;
            min-width: 100vw;
            height: 100vh;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        body {
            background: url('img/bg-ranking.png') no-repeat center center fixed;
            background-size: cover;
            color: #a3c2ff;
            font-family: 'PowerGrotesk-Regular', Arial, sans-serif;
            overflow: hidden;
        }
        .ranking-container {
            width: 1700px;
            margin: 40px auto 0 auto;
            background: rgba(10, 30, 80, 0.95);
            border-radius: 16px;
            box-shadow: 0 0 32px #000a;
            padding: 0;
            position: relative;
        }
        .ranking-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 48px 0 48px;
            height: 120px;
            background: #0f1027;
            border-radius: 16px 16px 0 0;
            position: relative;
            z-index: 2;
        }
        .ranking-title {
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            font-size: 5.5em;
            color: #7eb6ff;
            letter-spacing: 2px;
            flex: 1;
            z-index: 2;
        }
        .ranking-logo {
            position: absolute;
            top: -40px;
            right: 130px;
            height: 180px;
            width: auto;
            transform: rotate(-3deg);
            z-index: 3;
            filter: drop-shadow(0 4px 24px #0008);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: none;
        }
        th, td {
            padding: 18px 0;
            text-align: center;
            font-size: 1.7em;
            font-family: 'PowerGrotesk-Regular', Arial, sans-serif;
            letter-spacing: 1px;
        }
        th {
            background: #120eb2;
            color: #6699ff;
            font-family: 'PowerGrotesk-Regular', Arial, sans-serif;
            font-size: 1.45em;
            border-bottom: 4px solid #0a1e50;
            padding: 10px 20px 10px 20px;
        }
        tr {
            background: #163a8a;
            border-bottom: 2px solid #0a1e50;
        }
        tr:nth-child(even) {
            background: #090773;
        }
        .posicao {
            font-family: 'PowerGrotesk-Black', 'PowerGrotesk-Bold', Arial, sans-serif;
            font-size: 2.0em;
            color: #a3c2ff;
            font-style: normal;
        }
        .pontuacao {
            font-family: 'PowerGroteskHeavy', 'PowerGrotesk-Bold', Arial, sans-serif;
            font-size: 2.7em;
            color: #090773;
            background: #206aff;
            border-radius: 0 0 0 0;
            padding: 20px 100px 20px 100px;
        }
        .instituicao-ultrabold {
            font-family: 'PowerGrotesk-UltraBold', 'PowerGrotesk-Bold', Arial, sans-serif;
            font-weight: 900;
            letter-spacing: 1px;
            color: #a3c2ff;
            font-size: 1.6em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="ranking-container">
        <div class="ranking-header">
            <span class="ranking-title">RANKING DOS ALUNOS</span>
            <img src="img/als-2025.png" class="ranking-logo" alt="Logo Ranking" />
        </div>
        <table id="rankingTable">
            <thead>
                <tr>
                    <th>POSICAO</th>
                    <th>NOME</th>
                    <th>CURSO</th>
                    <th>INSTITUICAO</th>
                    <th>PONTUACAO</th>
                </tr>
            </thead>
            <tbody id="rankingBody">
                <!-- Dados via JS -->
            </tbody>
        </table>
    </div>
    <script>
    function atualizarRanking() {
        fetch('quiz/ranking_data.php')
            .then(resp => resp.json())
            .then(data => {
                console.log('Ranking recebido:', data);
                const tbody = document.getElementById('rankingBody');
                if (!tbody) return;
                tbody.innerHTML = '';
                data.forEach((item, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class='posicao'><b><i>#${idx+1}</i></b></td><td>${item.aluno_nome}</td><td>${item.curso}</td><td class='instituicao-ultrabold'>${item.instituicao_nome}</td><td class='pontuacao'>${item.pontuacao}</td>`;
                    tbody.appendChild(tr);
                });
            });
    }
    setInterval(atualizarRanking, 2000);
    atualizarRanking();
    </script>
</body>
</html> 