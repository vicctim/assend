<?php
spl_autoload_register(function ($class) {
    // Converte namespace para caminho do arquivo
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    
    // Se o arquivo existir, carrega-o
    if (file_exists($file)) {
        require $file;
        return true;
    }
    return false;
}); 