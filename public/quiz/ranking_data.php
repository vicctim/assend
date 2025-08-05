<?php
require_once __DIR__ . '/../../db/conexao.php';
header('Content-Type: application/json');
$conn = getDbConnection();
$res = $conn->query("SELECT aluno_nome, curso, instituicao_nome, pontuacao FROM ranking ORDER BY pontuacao DESC, aluno_nome ASC");
$ranking = [];
while ($row = $res->fetch_assoc()) {
    $ranking[] = $row;
}
$res->close();
$conn->close();
echo json_encode($ranking); 