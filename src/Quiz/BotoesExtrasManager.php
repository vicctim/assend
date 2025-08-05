<?php
namespace App\Quiz;

use App\Utils\SessionManager;

class BotoesExtrasManager {
    private $session;
    private const BOTOES = ['placas', 'pular', 'convidados'];
    
    public function __construct() {
        $this->session = SessionManager::getInstance();
    }
    
    public function getEstadoBotoes() {
        $estado = [];
        foreach (self::BOTOES as $botao) {
            $estado[$botao] = [
                'usado' => $this->session->isBotaoExtraUsado($botao),
                'confirmado' => $this->session->get("botao_{$botao}_confirmado", false)
            ];
        }
        return $estado;
    }
    
    public function tentarUsarBotao($botao) {
        if (!in_array($botao, self::BOTOES)) {
            throw new \Exception('Botão inválido');
        }
        
        if ($this->session->isBotaoExtraUsado($botao)) {
            return false;
        }
        
        $confirmado = $this->session->get("botao_{$botao}_confirmado", false);
        
        if (!$confirmado) {
            // Primeiro clique - apenas marca como confirmado
            $this->session->set("botao_{$botao}_confirmado", true);
            return 'confirmar';
        } else {
            // Segundo clique - usa o botão
            $this->session->setBotaoExtraUsado($botao);
            $this->session->set("botao_{$botao}_confirmado", false);
            return true;
        }
    }
    
    public function cancelarConfirmacao($botao) {
        if (!in_array($botao, self::BOTOES)) {
            throw new \Exception('Botão inválido');
        }
        
        $this->session->set("botao_{$botao}_confirmado", false);
    }
    
    public function resetarBotoes() {
        foreach (self::BOTOES as $botao) {
            $this->session->set("botao_{$botao}_confirmado", false);
        }
        $this->session->set('botoes_extras_usados', []);
    }
    
    public function getBotaoConfirmado() {
        foreach (self::BOTOES as $botao) {
            if ($this->session->get("botao_{$botao}_confirmado", false)) {
                return $botao;
            }
        }
        return null;
    }
} 