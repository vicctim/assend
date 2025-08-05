<?php
session_start();
header('Content-Type: application/json');

$instituicoes = isset($_SESSION['instituicoes_sorteadas']) ? $_SESSION['instituicoes_sorteadas'] : [];
$alunos = isset($_SESSION['alunos_sorteados']) ? $_SESSION['alunos_sorteados'] : [];

$timeline = [];
foreach ($instituicoes as $inst) {
    $inst_id = $inst['id'];
    $timeline[] = [
        'instituicao' => $inst,
        'alunos' => isset($alunos[$inst_id]) ? $alunos[$inst_id] : []
    ];
}
echo json_encode($timeline); 