<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['instituicoes_pool']) || count($_SESSION['instituicoes_pool']) === 0) {
    echo json_encode(['success'=>false, 'msg'=>'Nenhuma instituição disponível']);
    exit;
}
$instituicoes_pool = $_SESSION['instituicoes_pool'];
$idx = array_rand($instituicoes_pool);
$sorteada = $instituicoes_pool[$idx];
$_SESSION['instituicoes_sorteadas'][] = $sorteada;
array_splice($_SESSION['instituicoes_pool'], $idx, 1);
$_SESSION['instituicoes_pool'] = array_values($_SESSION['instituicoes_pool']);
echo json_encode(['success'=>true, 'instituicao'=>$sorteada]); 