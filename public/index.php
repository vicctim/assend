<?php
// Configurações de charset
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as configurações
require_once __DIR__ . '/../config/config.php';

// Carrega a conexão com o banco
require_once __DIR__ . '/../db/conexao.php';

// Usa o SessionManager
use App\Utils\SessionManager;

// Inicializa o SessionManager
$session = SessionManager::getInstance();

// Cabeçalhos para desabilitar cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quiz - Assend Show</title>
    <link rel="stylesheet" href="/assets/css/quiz.css?v=<?= time() ?>">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        #video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
        }
        #fallback-image {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        #fallback-image.fade-in {
            opacity: 1;
        }
        .menu-botoes-inicio {
            position: fixed;
            left: 0;
            bottom: 40px;
            width: 100vw;
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            justify-content: center;
            gap: 10px;
            z-index: 10;
        }
        .btn-inicio {
            width: 280px;
            max-width: 90vw;
            height: 70px;
            font-size: 1.0em;
            font-family: 'PowerGrotesk-Bold', Arial, sans-serif;
            border-radius: 18px;
            border: none;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #0b1e2d;
            color: #2ff0e6;
            box-shadow: #7affda 1px 1px 16px 1px;
            letter-spacing: 2px;
            font-weight: bold;
            transition: all 0.2s;
            text-shadow: 0 0 8px #3fffd6a0;
            text-decoration: none;
        }
        .btn-inicio:hover {
            background: #222;
            color: #1fffc6;
            box-shadow: 0 0 32px #1fffc6a0;
        }
    </style>
</head>
<body>
    <video id="video-background" autoplay muted playsinline>
        <source src="/assets/video/video-logo-assend.mp4" type="video/mp4">
    </video>
    <img id="fallback-image" src="/assets/video/frame-video-1.png" alt="Background">
    <div class="menu-botoes-inicio">
        <a href="/sorteio/instituicao.php" class="btn-inicio">SORTEAR INSTITUIÇÃO</a>
        <a href="/sorteio/aluno.php" class="btn-inicio">SORTEAR ALUNO</a>
        <a href="/quiz/index.php" class="btn-inicio">INICIAR QUIZ</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video-background');
            const fallbackImage = document.getElementById('fallback-image');
            
            video.addEventListener('ended', function() {
                // Esconde o vídeo
                video.style.opacity = '0';
                // Mostra a imagem com fade in
                fallbackImage.classList.add('fade-in');
            });
        });
    </script>
</body>
</html>