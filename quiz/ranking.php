<?php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ranking - Quiz</title>
    <link rel="stylesheet" href="assets/css/quiz.css?v=<?= time() ?>">
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
            <tbody>
                <!-- Dados via JS -->
            </tbody>
        </table>
    </div>
    <script>
    function atualizarRanking() {
        fetch('ranking_data.php')
            .then(resp => resp.json())
            .then(data => {
                const tbody = document.querySelector('#rankingTable tbody');
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