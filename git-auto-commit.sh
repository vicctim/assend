#!/bin/bash

# Script para commit automático após edições
# Uso: ./git-auto-commit.sh "mensagem do commit"

# Definir cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Mensagem padrão se não fornecida
COMMIT_MSG="${1:-Auto-commit: Update files}"

echo -e "${BLUE}[AUTO-COMMIT]${NC} Verificando mudanças..."

# Verificar se há mudanças
if [ -z "$(git status --porcelain)" ]; then
    echo -e "${GREEN}[AUTO-COMMIT]${NC} Nenhuma mudança detectada."
    exit 0
fi

# Mostrar status
echo -e "${BLUE}[AUTO-COMMIT]${NC} Mudanças detectadas:"
git status --short

# Adicionar todos os arquivos
git add .

# Fazer commit
echo -e "${BLUE}[AUTO-COMMIT]${NC} Fazendo commit..."
git commit -m "$COMMIT_MSG

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>"

# Push para o repositório remoto
echo -e "${BLUE}[AUTO-COMMIT]${NC} Enviando para GitHub..."
if git push origin main; then
    echo -e "${GREEN}[AUTO-COMMIT]${NC} Commit realizado e enviado com sucesso!"
else
    echo -e "${RED}[AUTO-COMMIT]${NC} Erro ao enviar para GitHub. Verificar conexão e permissões."
    exit 1
fi