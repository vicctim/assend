document.addEventListener('DOMContentLoaded', function() {
    const alternativas = document.querySelectorAll('.alternativa-btn');
    const overlays = {
        selecionada: 'img/facil/overlay-alternativa-selecionada.png',
        correta: 'img/facil/overlay-alternativa-correta.png',
        errada: 'img/facil/overlay-alternativa-errada.png',
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
                    document.getElementById('overlay-' + l).style.display = 'none';
                });
                selecionada = letra;
                // Overlay de seleção
                document.getElementById('overlay-' + letra).src = overlays.selecionada;
                document.getElementById('overlay-' + letra).style.display = 'block';
                confirmando = true;
            } else if (selecionada === letra && confirmando) {
                // Confirmação
                bloqueado = true;
                fetch('quiz_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'confirmar', letra: letra })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.correta) {
                        document.getElementById('overlay-' + letra).src = overlays.correta;
                        document.getElementById('overlay-' + letra).style.display = 'block';
                    } else {
                        document.getElementById('overlay-' + letra).src = overlays.errada;
                        document.getElementById('overlay-' + letra).style.display = 'block';
                        document.getElementById('overlay-' + data.correta_letra).src = overlays.correta;
                        document.getElementById('overlay-' + data.correta_letra).style.display = 'block';
                    }
                    podeAvancar = true;
                });
            }
        });
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Avançar para próxima pergunta: Ctrl + Alt + P
        if (e.ctrlKey && e.altKey && (e.key === 'p' || e.key === 'P')) {
            if (podeAvancar) {
                window.location.reload();
            }
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
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'categoria=' + encodeURIComponent(cat)
            }).then(() => { window.location.reload(); });
        }
    }
}); 