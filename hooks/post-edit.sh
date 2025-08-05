#!/bin/bash

# Hook que executa após edições de arquivos
# Automaticamente committa e envia mudanças para o GitHub

# Diretório do projeto
PROJECT_DIR="/projetoquizz"

# Navegar para o diretório do projeto
cd "$PROJECT_DIR" || exit 1

# Verificar se o diretório é um repositório git
if [ ! -d ".git" ]; then
    echo "Erro: Não é um repositório Git"
    exit 1
fi

# Executar auto-commit
./git-auto-commit.sh "Update: Files modified via Claude Code"