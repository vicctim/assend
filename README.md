# Sistema de Quiz - CREA

Sistema de quiz interativo para eventos ao vivo com perguntas relacionadas ao CREA (Conselho Regional de Engenharia e Agronomia).

## 📦 Instalação Automática em Novo Ambiente

### Pré-requisitos
- Docker
- Docker Compose

### Passo a Passo

1. **Clone/Copie o projeto completo**
   ```bash
   # Copie toda a pasta do projeto para o novo ambiente
   ```

2. **Configure as variáveis de ambiente** (opcional)
   ```bash
   cd /projetoquizz
   # Edite o arquivo .env se necessário para alterar domínio/porta
   ```

3. **Inicie o sistema** (TUDO AUTOMÁTICO)
   ```bash
   docker-compose up -d
   ```

4. **Acesse o sistema**
   - **Aplicação principal**: http://localhost
   - **Admin**: http://localhost/admin (admin/admin123)
   - **PhpMyAdmin**: http://localhost:8080

### 🎯 Banco de Dados Auto-Inicializado

O banco de dados é **automaticamente inicializado** com todos os dados necessários:

- ✅ **5 Instituições**: UFU, UNIARAXÁ, IFTM, CEFET, PARTICIPANTE INDIVIDUAL
- ✅ **35 Estudantes** de diferentes cursos de engenharia
- ✅ **201 Perguntas CREA** (94 fáceis, 61 médias, 46 difíceis)
- ✅ **316 Alternativas** com respostas corretas
- ✅ **Usuários admin** pré-configurados

## 🛠️ Comandos Úteis

### Parar os containers
```bash
docker-compose down
```

### Ver logs
```bash
docker-compose logs -f
```

### Executar comandos no container
```bash
docker-compose exec web bash
```

### Resetar o banco de dados
```bash
docker-compose down -v
docker-compose up -d
```

## 📁 Estrutura do Projeto

```
/projetoquizz/
├── docker/              # Configurações Docker
├── public/              # Arquivos públicos
├── src/                 # Código fonte (Classes PHP)
├── config/              # Configurações
├── admin/               # Painel administrativo
├── quiz/                # Sistema de quiz
├── sorteio/             # Sistema de sorteio
├── assets/              # CSS, JS, imagens
├── uploads/             # Uploads de arquivos
└── logs/                # Logs do sistema
```

## 🔧 Configuração para Produção

1. **Altere as senhas padrão:**
   - Senha do admin no banco
   - Credenciais do banco no `.env`

2. **Configure HTTPS:**
   - Use um proxy reverso (Nginx, Traefik)
   - Configure certificados SSL

3. **Ajuste permissões:**
```bash
docker-compose exec web chown -R www-data:www-data /var/www/html
```

4. **Configure backups do banco de dados**

## 🔄 Mudança de Domínio

Para usar um domínio diferente:

1. Edite o arquivo `.env`:
```bash
APP_DOMAIN=novo-dominio.com
```

2. Reinicie os containers:
```bash
docker-compose restart
```

## 📝 Funcionalidades

- Sistema de quiz com 3 níveis de dificuldade
- Sorteio de instituições e alunos
- Painel administrativo completo
- Importação/exportação de perguntas
- Sistema de ranking
- Opções especiais: Placas, Convidados, Pular
- Interface responsiva e moderna

## 🐛 Solução de Problemas

### Erro de conexão com banco de dados
```bash
# Verifique se o MySQL está rodando
docker-compose ps

# Verifique os logs
docker-compose logs mysql
```

### Erro de permissões
```bash
docker-compose exec web chmod -R 777 /var/www/html/uploads /var/www/html/logs /var/www/html/cache
```

### Limpar cache
```bash
docker-compose exec web rm -rf /var/www/html/cache/*
```

## 📞 Suporte

Para suporte e questões técnicas, consulte a documentação ou entre em contato com a equipe de desenvolvimento.