<?php
namespace App\Sorteio;

use App\Database\Database;
use App\Utils\SessionManager;

class SorteioManager {
    private $db;
    private $session;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->session = SessionManager::getInstance();
    }
    
    public function sortearInstituicao() {
        $sql = 'SELECT i.* FROM instituicoes i 
                LEFT JOIN sorteios s ON i.id = s.instituicao_id 
                WHERE s.id IS NULL 
                ORDER BY RAND() LIMIT 1';
        
        $instituicao = $this->db->fetch($sql);
        
        if ($instituicao) {
            $this->session->setInstituicaoSorteada($instituicao['id']);
        }
        
        return $instituicao;
    }
    
    public function sortearAluno() {
        $instituicaoId = $this->session->getInstituicaoSorteada();
        
        if (!$instituicaoId) {
            throw new \Exception('Nenhuma instituição sorteada');
        }
        
        $sql = 'SELECT a.* FROM alunos a 
                LEFT JOIN sorteios s ON a.id = s.aluno_id 
                WHERE a.instituicao_id = ? AND s.id IS NULL 
                ORDER BY RAND() LIMIT 1';
        
        $aluno = $this->db->fetch($sql, [$instituicaoId], 'i');
        
        if ($aluno) {
            $this->session->setAlunoSorteado($aluno['id']);
            $this->registrarSorteio($instituicaoId, $aluno['id']);
        }
        
        return $aluno;
    }
    
    private function registrarSorteio($instituicaoId, $alunoId) {
        $sql = 'INSERT INTO sorteios (instituicao_id, aluno_id, data_sorteio) 
                VALUES (?, ?, NOW())';
        
        return $this->db->query($sql, [$instituicaoId, $alunoId], 'ii');
    }
    
    public function getInstituicaoSorteada() {
        $instituicaoId = $this->session->getInstituicaoSorteada();
        
        if (!$instituicaoId) {
            return null;
        }
        
        $sql = 'SELECT * FROM instituicoes WHERE id = ?';
        return $this->db->fetch($sql, [$instituicaoId], 'i');
    }
    
    public function getAlunoSorteado() {
        $alunoId = $this->session->getAlunoSorteado();
        
        if (!$alunoId) {
            return null;
        }
        
        $sql = 'SELECT a.*, i.nome as instituicao_nome 
                FROM alunos a 
                JOIN instituicoes i ON a.instituicao_id = i.id 
                WHERE a.id = ?';
        
        return $this->db->fetch($sql, [$alunoId], 'i');
    }
    
    public function resetarSorteio() {
        $this->session->clearSorteio();
    }
    
    public function limparSorteios() {
        $sql = 'DELETE FROM sorteios';
        return $this->db->query($sql);
    }
    
    public function getHistoricoSorteios() {
        $sql = 'SELECT s.*, i.nome as instituicao_nome, a.nome as aluno_nome 
                FROM sorteios s 
                JOIN instituicoes i ON s.instituicao_id = i.id 
                JOIN alunos a ON s.aluno_id = a.id 
                ORDER BY s.data_sorteio DESC';
        
        return $this->db->fetchAll($sql);
    }
    
    public function verificarDisponibilidade() {
        $instituicaoId = $this->session->getInstituicaoSorteada();
        
        if (!$instituicaoId) {
            return [
                'instituicao' => false,
                'aluno' => false
            ];
        }
        
        $sql = 'SELECT COUNT(*) as total FROM alunos a 
                LEFT JOIN sorteios s ON a.id = s.aluno_id 
                WHERE a.instituicao_id = ? AND s.id IS NULL';
        
        $result = $this->db->fetch($sql, [$instituicaoId], 'i');
        
        return [
            'instituicao' => true,
            'aluno' => $result['total'] > 0
        ];
    }
} 