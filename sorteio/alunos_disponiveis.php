<?php
session_start();
require_once '../db/conexao.php';
header('Content-Type: application/json');

$instituicao_id = isset($_GET['instituicao_id']) ? intval($_GET['instituicao_id']) : 0;
if (!$instituicao_id) {
    echo json_encode([]);
    exit;
}

// Buscar todos os alunos da instituição
$conn = getDbConnection();
$stmt = $conn->prepare('SELECT id, nome FROM alunos WHERE instituicao_id = ?');
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
$alunos_sorteados = isset($_SESSION['alunos_sorteados'][$instituicao_id]) ? array_column($_SESSION['alunos_sorteados'][$instituicao_id], 'id') : [];
$alunos_disponiveis = array_filter($alunos, function($a) use ($alunos_sorteados) {
    return !in_array($a['id'], $alunos_sorteados);
});

// Reindexar
$alunos_disponiveis = array_values($alunos_disponiveis);
echo json_encode($alunos_disponiveis); 