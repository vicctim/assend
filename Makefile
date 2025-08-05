# Makefile para facilitar comandos Docker

.PHONY: help up down restart logs shell install clean reset

help:
	@echo "Comandos disponíveis:"
	@echo "  make up        - Inicia os containers"
	@echo "  make down      - Para os containers"
	@echo "  make restart   - Reinicia os containers"
	@echo "  make logs      - Exibe os logs"
	@echo "  make shell     - Acessa o shell do container web"
	@echo "  make install   - Instala as dependências"
	@echo "  make clean     - Limpa cache e logs"
	@echo "  make reset     - Reseta o banco de dados"

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

shell:
	docker-compose exec web bash

install:
	docker-compose exec web composer install
	docker-compose exec web chmod -R 777 /var/www/html/uploads /var/www/html/logs /var/www/html/cache

clean:
	docker-compose exec web rm -rf /var/www/html/cache/*
	docker-compose exec web rm -rf /var/www/html/logs/*

reset:
	docker-compose down -v
	docker-compose up -d
	@echo "Banco de dados resetado!"