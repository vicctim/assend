<?php
session_start();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../db/conexao.php';

if (!isset($_SESSION['instituicoes_pool'])) {
    // Carrega todas as instituições disponíveis para o pool de sorteio
    $conn = getDbConnection();
    $res = $conn->query('SELECT id, nome FROM instituicoes');
    $instituicoes = [];
    while ($row = $res->fetch_assoc()) {
        $instituicoes[] = $row;
    }
    $res->close();
    $conn->close();
    $_SESSION['instituicoes_pool'] = $instituicoes;
    $_SESSION['instituicoes_sorteadas'] = [];
    $_SESSION['alunos_sorteados'] = [];
}

$instituicoes_pool = $_SESSION['instituicoes_pool'];
$instituicoes_sorteadas = $_SESSION['instituicoes_sorteadas'];
$alunos_sorteados = $_SESSION['alunos_sorteados'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sortear_instituicao']) && count($instituicoes_pool) > 0) {
        $idx = array_rand($instituicoes_pool);
        $sorteada = $instituicoes_pool[$idx];
        $_SESSION['instituicoes_sorteadas'][] = $sorteada;
        array_splice($_SESSION['instituicoes_pool'], $idx, 1);
        header('Location: instituicao.php');
        exit;
    }
    if (isset($_POST['sortear_aluno']) && isset($_POST['instituicao_id'])) {
        $instituicao_id = intval($_POST['instituicao_id']);
        // Buscar alunos disponíveis para a instituição
        $conn = getDbConnection();
        $stmt = $conn->prepare('SELECT id, nome, curso, periodo FROM alunos WHERE instituicao_id = ?');
        $stmt->bind_param('i', $instituicao_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $alunos = [];
        while ($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
        $stmt->close();
        $conn->close();
        // Remover alunos já sorteados
        $ja_sorteados = isset($alunos_sorteados[$instituicao_id]) ? array_column($alunos_sorteados[$instituicao_id], 'id') : [];
        $alunos_disponiveis = array_filter($alunos, function($a) use ($ja_sorteados) {
            return !in_array($a['id'], $ja_sorteados);
        });
        if (count($alunos_disponiveis) > 0) {
            $alunos_disponiveis = array_values($alunos_disponiveis);
            $idx = array_rand($alunos_disponiveis);
            $sorteado = $alunos_disponiveis[$idx];
            $_SESSION['alunos_sorteados'][$instituicao_id][] = $sorteado;
        }
        header('Location: instituicao.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sorteio de Instituição - Assend Show</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            background:url('/img/bg-ranking.png') no-repeat center center fixed;
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
            justify-content: flex-start;
        }
        .instituicoes-sorteadas-row {
            display: flex;
            flex-direction: row;
            gap: 40px;
            margin-top: 80px;
            margin-bottom: 60px;
        }
        .instituicao-block {
            background: rgba(11, 25, 40, 0.36);
            border-radius: 20px;
            padding: 32px 48px 24px 48px;
            min-width: 340px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 0 30px 0 rgba(47,240,230,0.13);
            position: relative;
        }
        .instituicao-nome {
            font-size: 1.7em;
            font-weight: bold;
            text-shadow: 0 0 18px #2ff0e6;
            margin-bottom: 18px;
            text-align: center;
        }
        .btn-sortear-alunos {
            background: #2ff0e6;
            color: #0b1e2d;
            border: none;
            border-radius: 30px;
            padding: 15px 40px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 10px;
            transition: background 0.2s;
            box-shadow: 0 0 30px rgba(47, 240, 230, 0.2);
        }
        .btn-sortear-alunos:hover {
            background: #1ad1c1;
        }
        .alunos-list {
            width: 100%;
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .aluno-block {
            background: rgba(47,240,230,0.10);
            border-radius: 14px;
            padding: 12px 18px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 220px;
            box-shadow: 0 0 10px 0 rgba(47,240,230,0.10);
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .aluno-block:hover {
            background: rgba(47,240,230,0.18);
            box-shadow: 0 0 20px 0 rgba(47,240,230,0.18);
        }
        .aluno-nome {
            font-size: 1.1em;
            font-weight: bold;
            color: #2ff0e6;
        }
        .aluno-dados {
            font-size: 0.95em;
            color: #fff;
        }
        .btn-sortear-instituicao {
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
            margin-top: 180px;
        }
        .btn-sortear-instituicao:hover {
            transform: scale(1.05);
            box-shadow: 0 0 60px rgba(47, 240, 230, 0.6);
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(47, 240, 230, 0.6); }
            70% { box-shadow: 0 0 0 40px rgba(47, 240, 230, 0); }
            100% { box-shadow: 0 0 0 0 rgba(47, 240, 230, 0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <button id="btnSortearInstituicao" class="btn-sortear-instituicao" style="display:<?= count($instituicoes_pool) > 0 ? 'block' : 'none' ?>;margin-top:180px;">SORTEAR INSTITUIÇÃO</button>
        <div class="instituicoes-sorteadas-row" id="sorteadasRow">
            <!-- Instituições sorteadas serão renderizadas aqui -->
        </div>
    </div>
    <!-- Modal de animação de letreiro -->
    <div id="modalLetreiro" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(11,30,45,0.97);z-index:999;align-items:center;justify-content:center;">
        <div id="letreiroContent" style="font-size:4em;color:#2ff0e6;text-shadow:0 0 30px #2ff0e6;font-weight:bold;letter-spacing:8px;text-align:center;width:100vw;"></div>
    </div>
    <script>
    let instituicoesPool = <?= json_encode(array_values($instituicoes_pool)) ?>;
    let instituicoesSorteadas = <?= json_encode(array_values($instituicoes_sorteadas)) ?>;
    let alunosSorteados = <?= json_encode($alunos_sorteados) ?>;

    function renderSorteadas() {
        const row = document.getElementById('sorteadasRow');
        row.innerHTML = '';
        instituicoesSorteadas.forEach(inst => {
            const div = document.createElement('div');
            div.className = 'instituicao-block';
            div.innerHTML = `<div class='instituicao-nome'>${inst.nome}</div>
                <form class='formSortearAluno' data-id='${inst.id}' style='margin:0;'>
                    <button type='submit' class='btn-sortear-alunos'>Sortear Alunos</button>
                </form>
                <div class='alunos-list' id='alunos-list-${inst.id}'>${renderAlunos(inst.id)}</div>`;
            row.appendChild(div);
        });
        bindSortearAlunos();
    }
    function renderAlunos(instId) {
        if (!alunosSorteados[instId]) return '';
        return alunosSorteados[instId].map(aluno =>
            `<div class='aluno-block' data-aluno-id='${aluno.id}'>
                <span class='aluno-nome'>${aluno.nome}</span>
                <span class='aluno-dados'>${aluno.curso} | ${aluno.periodo}º período</span>
            </div>`
        ).join('');
    }
    function bindSortearAlunos() {
        document.querySelectorAll('.formSortearAluno').forEach(form => {
            form.onsubmit = function(e) {
                e.preventDefault();
                const instituicaoId = this.getAttribute('data-id');
                fetch('alunos_disponiveis.php?instituicao_id=' + instituicaoId)
                    .then(resp => resp.json())
                    .then(alunos => {
                        if (!alunos.length) return;
                        showLetreiro(alunos.map(a => a.nome), (nomeSorteado) => {
                            // Encontrar o aluno pelo nome (idealmente pelo ID, mas mantendo por nome para compatibilidade)
                            const aluno = alunos.find(a => a.nome === nomeSorteado);
                            if (!aluno) return;
                            fetch('sortear_aluno.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'instituicao_id=' + instituicaoId + '&aluno_id=' + aluno.id
                            })
                            .then(resp => resp.json())
                            .then(data => {
                                if (data.success) {
                                    if (!alunosSorteados[instituicaoId]) alunosSorteados[instituicaoId] = [];
                                    alunosSorteados[instituicaoId] = data.alunos_sorteados;
                                    document.getElementById('alunos-list-' + instituicaoId).innerHTML = renderAlunos(instituicaoId);
                                    bindAlunoClicks();
                                }
                            });
                        });
                    });
            };
        });
    }
    function bindAlunoClicks() {
        document.querySelectorAll('.aluno-block').forEach(div => {
            div.onclick = function() {
                const alunoId = this.getAttribute('data-aluno-id');
                window.open('/quiz/index.php?aluno_id=' + alunoId, '_blank');
            };
        });
    }
    document.getElementById('btnSortearInstituicao').onclick = function() {
        if (instituicoesPool.length === 0) return;
        showLetreiro(instituicoesPool.map(i => i.nome), () => {
            fetch('sortear_instituicao.php')
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        instituicoesSorteadas.push(data.instituicao);
                        instituicoesPool = instituicoesPool.filter(i => i.id !== data.instituicao.id);
                        renderSorteadas();
                        if (instituicoesPool.length === 0) document.getElementById('btnSortearInstituicao').style.display = 'none';
                    }
                });
        });
    };
    function showLetreiro(nomes, onFinish) {
        const modal = document.getElementById('modalLetreiro');
        const content = document.getElementById('letreiroContent');
        modal.style.display = 'flex';
        let idx = 0, spinCount = 0, maxSpins = 40, interval = 50, slowDown = 25;
        function spin() {
            content.textContent = nomes[idx];
            idx = (idx + 1) % nomes.length;
            spinCount++;
            if (spinCount > slowDown) interval += 20;
            if (spinCount < maxSpins) {
                setTimeout(spin, interval);
            } else {
                setTimeout(() => {
                    modal.style.display = 'none';
                    // Retorna o nome sorteado
                    onFinish(nomes[(idx - 1 + nomes.length) % nomes.length]);
                }, 1200);
            }
        }
        spin();
    }
    // Inicialização
    renderSorteadas();
    bindAlunoClicks();
    </script>
</body>
</html> 