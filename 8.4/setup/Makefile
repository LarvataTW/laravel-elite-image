.PHONY: up
## up: up docker container
up: setup
	docker-compose up -d

.PHONY: down
## down: down docker container
down:
	docker-compose down

.PHONY: bash
## bash: docker container shell
bash: setup
	docker-compose exec web bash

.PHONY: setup
## setup: setup docker container
setup:
	@echo "setup docker container"
	@if [ ! -f "docker-compose.yml" ]; then cp docker-compose.example.yml docker-compose.yml; fi
	@if [ ! -d "node_modules" ]; then docker-compose run --rm web npm install; fi
	@if [ ! -d "vendor" ]; then docker-compose run --rm web composer install; fi
	@if [ ! -f ".env" ]; then cp .env.example .env && docker-compose run --rm web php artisan key:generate; fi

.PHONY: help
## help: prints this help message
help:
	@echo "Usage: \n"
	@sed -n 's/^##//p' ${MAKEFILE_LIST} | column -t -s ':' |  sed -e 's/^/ /'
