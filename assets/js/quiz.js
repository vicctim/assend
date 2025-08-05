// JS principal do Quiz - modularizado 

document.addEventListener('DOMContentLoaded', function() {
    const alternativas = document.querySelectorAll('.alternativa-btn');
    const categoria = document.body.classList.contains('quiz-medio') ? 'medio' : (document.body.classList.contains('quiz-dificil') ? 'dificil' : 'facil');
    // Detecta o prefixo correto para o caminho das imagens
    let imgPrefix = '';
    if (window.location.pathname.includes('/quiz/')) {
        imgPrefix = '../img';
    } else {
        imgPrefix = 'img';
    }
    const overlays = {
        selecionada: `${imgPrefix}/${categoria}/overlay-alternativa-selecionada.png`,
        correta: `${imgPrefix}/${categoria}/overlay-alternativa-correta.png`,
        errada: `${imgPrefix}/${categoria}/overlay-alternativa-errada.png`,
    };
    let selecionada = null;
    let confirmando = false;
    let bloqueado = false;
    let podeAvancar = false;

    // Animação de entrada dos textos
    const pergunta = document.getElementById('pergunta-texto');
    if (pergunta) {
        pergunta.style.opacity = 0;
        setTimeout(() => { pergunta.style.transition = 'opacity 0.7s'; pergunta.style.opacity = 1; }, 200);
    }
    alternativas.forEach((btn, i) => {
        btn.style.opacity = 0;
        setTimeout(() => {
            btn.style.transition = 'opacity 0.7s';
            btn.style.opacity = 1;
        }, 400 + i * 200);
    });

    // Seleção e confirmação
    alternativas.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (bloqueado) return;
            const letra = btn.dataset.letra;
            if (!selecionada || selecionada !== letra) {
                // Limpa overlays de seleção anteriores
                alternativas.forEach(b => {
                    const l = b.dataset.letra;
                    const overlay = document.getElementById('overlay-' + l);
                    if (overlay) {
                        overlay.style.display = 'none';
                    }
                });
                selecionada = letra;
                // Overlay de seleção
                const overlay = document.getElementById('overlay-' + letra);
                if (overlay) {
                    overlay.src = overlays.selecionada;
                    overlay.style.display = 'block';
                }
                confirmando = true;
            } else if (selecionada === letra && confirmando) {
                // Confirmação
                bloqueado = true;
                // Corrige o endpoint para funcionar em ambos os contextos
                let backendUrl = '';
                if (window.location.pathname.includes('/quiz/')) {
                    backendUrl = '../quiz_backend.php';
                } else {
                    backendUrl = 'quiz_backend.php';
                }
                fetch(backendUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'confirmar', letra: letra, pergunta_id: window.PERGUNTA_ID, aluno_id: window.ALUNO_ID })
                })
                .then(res => res.text())
                .then(text => {
                    console.log('RESPOSTA BRUTA:', text);
                    let data = {};
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Erro ao fazer parse do JSON:', e, text);
                        bloqueado = false;
                        return;
                    }
                    if (data.correta) {
                        if (data.vencedor) {
                            // Última da difícil: vencedor
                            const perguntaDiv = document.getElementById('pergunta-texto');
                            if (perguntaDiv) perguntaDiv.innerHTML = '<div class="parabens-msg vencedor">Parabéns, você é o vencedor!</div>';
                            const alternativasDiv = document.querySelector('.alternativas');
                            if (alternativasDiv) alternativasDiv.style.display = 'none';
                            const opcoesDiv = document.querySelector('.quiz-opcoes-extra, .botoes-extras');
                            if (opcoesDiv) {
                                opcoesDiv.innerHTML = '<button id="btn-continuar" class="btn-continuar">Finalizar</button>';
                                document.getElementById('btn-continuar').addEventListener('click', function() {
                                    window.location.reload();
                                });
                            }
                            document.body.classList.add('parabens-nivel');
                            return;
                        } else if (data.proxima_categoria) {
                            // Esconde pergunta e alternativas
                            const perguntaDiv = document.getElementById('pergunta-texto');
                            if (perguntaDiv) perguntaDiv.innerHTML = '<div class="parabens-msg">Parabéns! Vamos para o próximo nível.</div>';
                            const alternativasDiv = document.querySelector('.alternativas');
                            if (alternativasDiv) alternativasDiv.style.display = 'none';
                            // Esconde botões extras e mostra botão Continuar
                            const opcoesDiv = document.querySelector('.quiz-opcoes-extra, .botoes-extras');
                            if (opcoesDiv) {
                                opcoesDiv.innerHTML = '<button id="btn-continuar" class="btn-continuar">Continuar</button>';
                                document.getElementById('btn-continuar').addEventListener('click', function() {
                                    window.location.reload();
                                });
                            }
                            document.body.classList.add('parabens-nivel');
                            return;
                        }
                        const overlay = document.getElementById('overlay-' + letra);
                        if (overlay) {
                            overlay.src = overlays.correta;
                            overlay.style.display = 'block';
                            overlay.classList.add('piscar-correta');
                            setTimeout(() => {
                                overlay.classList.remove('piscar-correta');
                                overlay.style.opacity = 1;
                            }, 2000);
                        }
                    } else {
                        const overlayErrada = document.getElementById('overlay-' + letra);
                        const overlayCorreta = document.getElementById('overlay-' + data.correta_letra);
                        if (overlayErrada) {
                            overlayErrada.src = overlays.errada;
                            overlayErrada.style.display = 'block';
                        }
                        if (overlayCorreta) {
                            overlayCorreta.src = overlays.correta;
                            overlayCorreta.style.display = 'block';
                            overlayCorreta.classList.add('piscar-correta');
                            setTimeout(() => {
                                overlayCorreta.classList.remove('piscar-correta');
                                overlayCorreta.style.opacity = 1;
                            }, 2000);
                        }
                        // Adiciona flag para indicar que errou
                        window.ERROU_RESPOSTA = true;
                    }
                    podeAvancar = true;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    bloqueado = false;
                });
            }
        });
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Avançar para próxima pergunta: Ctrl + Alt + P
        if (e.ctrlKey && e.altKey && (e.key === 'p' || e.key === 'P')) {
            if (podeAvancar) {
                if (window.ERROU_RESPOSTA) {
                    // Se errou, mostra mensagem de erro
                    const perguntaDiv = document.getElementById('pergunta-texto');
                    if (perguntaDiv) perguntaDiv.innerHTML = '<div class="parabens-msg erro">Que pena, você errou. :(</div>';
                    const alternativasDiv = document.querySelector('.alternativas');
                    if (alternativasDiv) alternativasDiv.style.display = 'none';
                    const opcoesDiv = document.querySelector('.quiz-opcoes-extra, .botoes-extras');
                    if (opcoesDiv) {
                        opcoesDiv.innerHTML = '<button id="btn-continuar" class="btn-continuar">Continuar</button>';
                        document.getElementById('btn-continuar').addEventListener('click', function() {
                            // Limpa a sessão do quiz antes de redirecionar
                            let resetUrl = '';
                            if (window.location.pathname.includes('/quiz/')) {
                                resetUrl = '../quiz_reset.php';
                            } else {
                                resetUrl = 'quiz_reset.php';
                            }
                            fetch(resetUrl, { method: 'POST' })
                                .then(() => {
                                    // Redireciona para a página inicial
                                    let indexUrl = '';
                                    if (window.location.pathname.includes('/quiz/')) {
                                        indexUrl = '../index.php';
                                    } else {
                                        indexUrl = 'index.php';
                                    }
                                    window.location.href = indexUrl;
                                });
                        });
                    }
                    document.body.classList.add('parabens-nivel');
                    return;
                }
                window.location.reload();
            }
        }
        // Resetar botões extras: Ctrl + Alt + R
        let apiBotoesUrl = '';
        if (window.location.pathname.includes('/quiz/')) {
            apiBotoesUrl = '../api/botoes-extras.php';
        } else {
            apiBotoesUrl = 'api/botoes-extras.php';
        }
        if (e.ctrlKey && e.altKey && (e.key === 'r' || e.key === 'R')) {
            fetch(apiBotoesUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reset' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    atualizarEstadoBotoes(data.estado);
                }
            });
        }
        // Trocar para categoria difícil: Ctrl + Alt + D
        if (e.ctrlKey && e.altKey && (e.key === 'd' || e.key === 'D')) {
            trocarCategoria('dificil');
        }
        // Trocar para categoria fácil: Ctrl + Alt + F
        if (e.ctrlKey && e.altKey && (e.key === 'f' || e.key === 'F')) {
            trocarCategoria('facil');
        }
        // Trocar para categoria médio: Ctrl + Alt + M
        if (e.ctrlKey && e.altKey && (e.key === 'm' || e.key === 'M')) {
            trocarCategoria('medio');
        }
    });

    function trocarCategoria(cat) {
        // Procura o form de categoria e envia
        const form = document.querySelector('form');
        if (form) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'categoria';
            input.value = cat;
            form.appendChild(input);
            form.submit();
        } else {
            // Se não houver form, faz POST manual
            let postUrl = '';
            if (window.location.pathname.includes('/quiz/')) {
                postUrl = '../index.php';
            } else {
                postUrl = 'index.php';
            }
            fetch(postUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'categoria=' + encodeURIComponent(cat)
            }).then(() => { window.location.reload(); });
        }
    }

    // Lógica dos botões extras (PLACAS, CONVIDADOS, PULAR)
    const opcoesBtns = document.querySelectorAll('.quiz-opcao-btn');
    let preconfirm = null;
    opcoesBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (btn.classList.contains('usado') || btn.disabled) return;
            const opcao = btn.dataset.opcao;
            // Se já está em pré-confirmação, confirma
            if (preconfirm === opcao) {
                // Confirma escolha
                btn.classList.remove('preconfirm');
                btn.classList.add('usado');
                btn.disabled = true;
                preconfirm = null;
                // Envia confirmação para backend
                let backendUrl = '';
                if (window.location.pathname.includes('/quiz/')) {
                    backendUrl = '../quiz_backend.php';
                } else {
                    backendUrl = 'quiz_backend.php';
                }
                fetch(backendUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'opcao_extra', opcao: opcao, aluno_id: window.ALUNO_ID })
                })
                .then(res => res.json())
                .then(data => {
                    // Se for pular, recarrega para próxima pergunta
                    if (opcao === 'pular') {
                        fetch(backendUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ acao: 'pular', aluno_id: window.ALUNO_ID })
                        })
                        .then(() => {
                            window.location.reload();
                        });
                    }
                });
            } else {
                // Remove pré-confirmação de outros
                opcoesBtns.forEach(b => b.classList.remove('preconfirm'));
                btn.classList.add('preconfirm');
                preconfirm = opcao;
            }
        });
    });

    // Exemplo para carregar estado inicial dos botões:
    fetch(apiBotoesUrl)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                atualizarEstadoBotoes(data.estado);
            }
        })
        .catch(error => console.error('Erro:', error));
}); 