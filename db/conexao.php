<?php
require_once __DIR__ . '/../config/config.php';

function getDbConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($conn->connect_error) {
        // Em produção, não exibir detalhes do erro
        if (getenv('APP_ENV') === 'development') {
            die('Erro de conexão: ' . $conn->connect_error);
        } else {
            die('Erro ao conectar com o banco de dados');
        }
    }
    
    // Define charset UTF-8
    $conn->set_charset('utf8mb4');
    
    return $conn;
}