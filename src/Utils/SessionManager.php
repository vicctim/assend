<?php
namespace App\Utils;

class SessionManager {
    private static $instance = null;
    
    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
        session_write_close();
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            session_write_close();
        }
    }
    
    public function clear() {
        session_destroy();
        $_SESSION = [];
    }
    
    public function regenerate() {
        session_regenerate_id(true);
    }
    
    // Métodos específicos para o Quiz
    public function setQuizCategory($category) {
        $this->set('quiz_categoria', $category);
        $this->set('quiz_respondidas', []);
        $this->set('quiz_pulada', false);
    }
    
    public function getQuizCategory() {
        return $this->get('quiz_categoria', '');
    }
    
    public function addRespondida($perguntaId) {
        $respondidas = $this->get('quiz_respondidas', []);
        $respondidas[] = $perguntaId;
        $this->set('quiz_respondidas', $respondidas);
    }
    
    public function getRespondidas() {
        return $this->get('quiz_respondidas', []);
    }
    
    public function setPulada($value) {
        $this->set('quiz_pulada', $value);
    }
    
    public function isPulada() {
        return $this->get('quiz_pulada', false);
    }
    
    // Métodos para controle de botões extras
    public function setBotaoExtraUsado($botao) {
        $botoesUsados = $this->get('botoes_extras_usados', []);
        $botoesUsados[$botao] = true;
        $this->set('botoes_extras_usados', $botoesUsados);
    }
    
    public function isBotaoExtraUsado($botao) {
        $botoesUsados = $this->get('botoes_extras_usados', []);
        return isset($botoesUsados[$botao]) && $botoesUsados[$botao];
    }
    
    // Métodos para controle de sorteio
    public function setInstituicaoSorteada($instituicaoId) {
        $this->set('instituicao_sorteada', $instituicaoId);
    }
    
    public function getInstituicaoSorteada() {
        return $this->get('instituicao_sorteada');
    }
    
    public function setAlunoSorteado($alunoId) {
        $this->set('aluno_sorteado', $alunoId);
    }
    
    public function getAlunoSorteado() {
        return $this->get('aluno_sorteado');
    }
    
    public function clearSorteio() {
        $this->remove('instituicao_sorteada');
        $this->remove('aluno_sorteado');
    }
} 