<?php
session_start();
unset($_SESSION['quiz_categoria']);
unset($_SESSION['quiz_respondidas']);
unset($_SESSION['quiz_acertos']);
unset($_SESSION['quiz_pulada']);
unset($_SESSION['quiz_opcoes_usadas']);
echo json_encode(['reset' => true]); 