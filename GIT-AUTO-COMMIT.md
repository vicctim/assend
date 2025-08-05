# Sistema de Commit Automático

Este sistema permite commit e push automático para o GitHub sempre que arquivos são modificados.

## Arquivos

- `git-auto-commit.sh` - Script principal para commit automático
- `hooks/post-edit.sh` - Hook que executa após edições

## Como Usar

### Commit Manual
```bash
./git-auto-commit.sh "Mensagem do commit"
```

### Commit Automático após edições
O sistema monitora mudanças e automaticamente:
1. Adiciona todos os arquivos modificados
2. Faz commit com mensagem padrão
3. Envia para o repositório GitHub

## Funcionalidades

- ✅ Detecção automática de mudanças
- ✅ Commits com mensagens padronizadas
- ✅ Push automático para GitHub
- ✅ Logs coloridos para melhor visualização
- ✅ Tratamento de erros

## Mensagens de Commit

Todas as mensagens incluem:
- Descrição da mudança
- Assinatura do Claude Code
- Co-autoria com Claude

## Configuração

O sistema está configurado para:
- Repositório: https://github.com/vicctim/assend
- Branch: main
- Usuário: victorsamuel@outlook.com / vicctim