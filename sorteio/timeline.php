<?php
session_start();

// Estrutura esperada na sessão:
// $_SESSION['timeline'] = [
//   ['instituicao' => [...], 'alunos' => [[...], ...]], ...
// ];

function getTimeline() {
    return isset($_SESSION['timeline']) ? $_SESSION['timeline'] : [];
}

$timeline = getTimeline();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Timeline do Sorteio - Assend Show</title>
    <style>
        body {
            background: #0b1e2d;
            color: #2ff0e6;
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow-x: hidden;
        }
        .timeline-container {
            width: 1600px;
            margin: 40px auto;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 40px;
        }
        .instituicao-block {
            background: rgba(255,255,255,0.08);
            border-radius: 18px;
            box-shadow: 0 0 30px 0 rgba(47,240,230,0.13);
            padding: 32px 48px;
            margin-bottom: 0;
            min-width: 600px;
            position: relative;
            transition: box-shadow 0.3s;
        }
        .instituicao-block:hover {
            box-shadow: 0 0 60px 0 rgba(47,240,230,0.25);
        }
        .instituicao-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .instituicao-nome {
            font-size: 2em;
            font-weight: bold;
            text-shadow: 0 0 18px #2ff0e6;
        }
        .btn-remover-inst {
            background: #ff3b3b;
            color: #fff;
            border: none;
            border-radius: 18px;
            padding: 8px 22px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-left: 24px;
            transition: background 0.2s;
        }
        .btn-remover-inst:hover {
            background: #c90000;
        }
        .alunos-list {
            margin-left: 32px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .aluno-block {
            background: rgba(47,240,230,0.10);
            border-radius: 14px;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 400px;
            box-shadow: 0 0 10px 0 rgba(47,240,230,0.10);
        }
        .aluno-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .aluno-nome {
            font-size: 1.3em;
            font-weight: bold;
            color: #2ff0e6;
        }
        .aluno-dados {
            font-size: 1em;
            color: #fff;
        }
        .btn-iniciar-quiz {
            background: #2ff0e6;
            color: #0b1e2d;
            border: none;
            border-radius: 14px;
            padding: 10px 28px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            margin-left: 24px;
            transition: background 0.2s;
            text-decoration: none;
        }
        .btn-iniciar-quiz:hover {
            background: #1ad1c1;
        }
        .btn-remover-aluno {
            background: #ff3b3b;
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 8px 18px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-left: 18px;
            transition: background 0.2s;
        }
        .btn-remover-aluno:hover {
            background: #c90000;
        }
        .linha-timeline {
            width: 4px;
            background: #2ff0e6;
            min-height: 40px;
            margin: 0 0 0 18px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="timeline-container" id="timelineContainer">
        <!-- Timeline será preenchida via JS/AJAX -->
    </div>
    <script>
    function renderTimeline(timeline) {
        const container = document.getElementById('timelineContainer');
        container.innerHTML = '';
        timeline.forEach((item, idx) => {
            const instBlock = document.createElement('div');
            instBlock.className = 'instituicao-block';
            // Header da instituição
            const header = document.createElement('div');
            header.className = 'instituicao-header';
            header.innerHTML = `<span class='instituicao-nome'>${item.instituicao.nome}</span>` +
                `<button class='btn-remover-inst' onclick='removerInstituicao(${idx})'>Remover</button>`;
            instBlock.appendChild(header);
            // Lista de alunos
            const alunosList = document.createElement('div');
            alunosList.className = 'alunos-list';
            (item.alunos || []).forEach((aluno, aidx) => {
                const alunoBlock = document.createElement('div');
                alunoBlock.className = 'aluno-block';
                alunoBlock.innerHTML = `
                    <div class='aluno-info'>
                        <span class='aluno-nome'>${aluno.nome}</span>
                        <span class='aluno-dados'>${aluno.curso} | ${aluno.periodo}º período</span>
                        <span class='aluno-dados'>${item.instituicao.nome}</span>
                    </div>
                    <div>
                        <a class='btn-iniciar-quiz' href='/quiz/index.php?aluno_id=${aluno.id}' target='_blank'>Iniciar Quiz</a>
                        <button class='btn-remover-aluno' onclick='removerAluno(${idx},${aidx})'>Remover</button>
                    </div>
                `;
                alunosList.appendChild(alunoBlock);
            });
            instBlock.appendChild(alunosList);
            // Linha timeline (exceto último)
            if (idx < timeline.length - 1) {
                const linha = document.createElement('div');
                linha.className = 'linha-timeline';
                instBlock.appendChild(linha);
            }
            container.appendChild(instBlock);
        });
    }
    function atualizarTimeline() {
        fetch('timeline_data.php')
            .then(resp => resp.json())
            .then(data => renderTimeline(data));
    }
    function removerInstituicao(idx) {
        if (!confirm('Remover esta instituição e todos os alunos sorteados?')) return;
        fetch('timeline_data.php?action=remover_instituicao&idx=' + idx)
            .then(() => atualizarTimeline());
    }
    function removerAluno(idx, aidx) {
        if (!confirm('Remover este aluno da timeline?')) return;
        fetch('timeline_data.php?action=remover_aluno&idx=' + idx + '&aidx=' + aidx)
            .then(() => atualizarTimeline());
    }
    setInterval(atualizarTimeline, 2000);
    atualizarTimeline();
    </script>
</body>
</html> 