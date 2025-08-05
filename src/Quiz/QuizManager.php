<?php
namespace App\Quiz;

use App\Database\Database;
use App\Utils\SessionManager;

class QuizManager {
    private $db;
    private $session;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->session = SessionManager::getInstance();
    }
    
    public function iniciarQuiz($categoria) {
        $this->session->setQuizCategory($categoria);
        return $this->getProximaPergunta();
    }
    
    public function getProximaPergunta() {
        $categoria = $this->session->getQuizCategory();
        $respondidas = $this->session->getRespondidas();
        
        if (empty($categoria)) {
            return null;
        }
        
        $sql = 'SELECT * FROM perguntas WHERE categoria = ?';
        $params = [$categoria];
        $types = 's';
        
        if (!empty($respondidas)) {
            $placeholders = implode(',', array_fill(0, count($respondidas), '?'));
            $sql .= ' AND id NOT IN (' . $placeholders . ')';
            $params = array_merge($params, $respondidas);
            $types .= str_repeat('i', count($respondidas));
        }
        
        $sql .= ' ORDER BY RAND() LIMIT 1';
        
        $pergunta = $this->db->fetch($sql, $params, $types);
        
        if ($pergunta) {
            $pergunta['alternativas'] = $this->getAlternativas($pergunta['id']);
        }
        
        return $pergunta;
    }
    
    public function getAlternativas($perguntaId) {
        $sql = 'SELECT * FROM alternativas WHERE pergunta_id = ? ORDER BY letra';
        return $this->db->fetchAll($sql, [$perguntaId], 'i');
    }
    
    public function verificarResposta($perguntaId, $resposta) {
        $sql = 'SELECT * FROM alternativas WHERE pergunta_id = ? AND letra = ? AND correta = 1';
        $result = $this->db->fetch($sql, [$perguntaId, $resposta], 'is');
        
        $acertou = $result !== null;
        
        if ($acertou) {
            $this->registrarAcerto($perguntaId);
        }
        
        $this->session->addRespondida($perguntaId);
        
        return [
            'acertou' => $acertou,
            'resposta_correta' => $result ? $result['letra'] : null
        ];
    }
    
    private function registrarAcerto($perguntaId) {
        $acertos = $this->session->get('quiz_acertos', 0) + 1;
        $this->session->set('quiz_acertos', $acertos);
        
        if ($acertos >= ACERTOS_PARA_PROXIMA_CATEGORIA) {
            $this->avancarCategoria();
        }
    }
    
    private function avancarCategoria() {
        $categorias = QUIZ_CATEGORIES;
        $categoriaAtual = $this->session->getQuizCategory();
        $indexAtual = array_search($categoriaAtual, $categorias);
        
        if ($indexAtual !== false && isset($categorias[$indexAtual + 1])) {
            $this->session->setQuizCategory($categorias[$indexAtual + 1]);
            $this->session->set('quiz_acertos', 0);
        }
    }
    
    public function pularPergunta() {
        if (!$this->session->isPulada()) {
            $this->session->setPulada(true);
            return true;
        }
        return false;
    }
    
    public function usarBotaoExtra($botao) {
        if (!$this->session->isBotaoExtraUsado($botao)) {
            $this->session->setBotaoExtraUsado($botao);
            return true;
        }
        return false;
    }
    
    public function registrarAuditoria($dados) {
        $sql = 'INSERT INTO auditoria (aluno_id, pergunta_id, resposta_dada, acertou, data_resposta) 
                VALUES (?, ?, ?, ?, NOW())';
        
        $params = [
            $dados['aluno_id'],
            $dados['pergunta_id'],
            $dados['resposta_dada'],
            $dados['acertou'] ? 1 : 0
        ];
        
        $types = 'iisi';
        
        return $this->db->query($sql, $params, $types);
    }
    
    public function resetarQuiz() {
        $this->session->set('quiz_categoria', '');
        $this->session->set('quiz_respondidas', []);
        $this->session->set('quiz_pulada', false);
        $this->session->set('quiz_acertos', 0);
        $this->session->set('botoes_extras_usados', []);
    }
} 