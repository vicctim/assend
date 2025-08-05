<?php
session_start();
header('Content-Type: application/json');
require_once '../db/conexao.php';

$instituicao_id = isset($_POST['instituicao_id']) ? intval($_POST['instituicao_id']) : 0;
if (!$instituicao_id) {
    echo json_encode(['success'=>false, 'msg'=>'Instituição inválida']);
    exit;
}
$aluno_id = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : null;
// Buscar alunos disponíveis
$conn = getDbConnection();
$stmt = $conn->prepare('SELECT id, nome, curso, periodo FROM alunos WHERE instituicao_id = ?');
$stmt->bind_param('i', $instituicao_id);
$stmt->execute();
$result = $stmt->get_result();
$alunos = [];
while ($row = $result->fetch_assoc()) {
    $alunos[] = $row;
}
$stmt->close();
$conn->close();
$alunos_sorteados = isset($_SESSION['alunos_sorteados'][$instituicao_id]) ? array_column($_SESSION['alunos_sorteados'][$instituicao_id], 'id') : [];
$alunos_disponiveis = array_filter($alunos, function($a) use ($alunos_sorteados) {
    return !in_array($a['id'], $alunos_sorteados);
});
$alunos_disponiveis = array_values($alunos_disponiveis);
if (count($alunos_disponiveis) === 0) {
    echo json_encode(['success'=>false, 'msg'=>'Nenhum aluno disponível']);
    exit;
}
if ($aluno_id) {
    // Sorteio já feito no frontend, apenas registre
    $sorteado = null;
    foreach ($alunos_disponiveis as $a) {
        if ($a['id'] == $aluno_id) {
            $sorteado = $a;
            break;
        }
    }
    if (!$sorteado) {
        echo json_encode(['success'=>false, 'msg'=>'Aluno não disponível']);
        exit;
    }
} else {
    $idx = array_rand($alunos_disponiveis);
    $sorteado = $alunos_disponiveis[$idx];
}
$_SESSION['alunos_sorteados'][$instituicao_id][] = $sorteado;
echo json_encode(['success'=>true, 'aluno'=>$sorteado, 'alunos_sorteados'=>$_SESSION['alunos_sorteados'][$instituicao_id]]); 