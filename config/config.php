<?php
// Carrega variáveis de ambiente
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove aspas se existirem
            $value = trim($value, '"\'');
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Configurações do ambiente
$APP_ENV = getenv('APP_ENV') ?: 'production';
$APP_DOMAIN = getenv('APP_DOMAIN') ?: 'localhost';

// Configurações de erro baseadas no ambiente
if ($APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Configurações do banco de dados
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'quiz_db';

// Configurações de localização e charset
setlocale(LC_ALL, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil');
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Timezone
date_default_timezone_set(getenv('TIMEZONE') ?: 'America/Sao_Paulo');