DOCKER_COMPOSE = docker-compose
PHP_CONTAINER = app

.PHONY: up down restart logs shell php-shell cache-clear db-create db-schema-update db-drop db-seed test install build

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

restart:
	$(DOCKER_COMPOSE) down && $(DOCKER_COMPOSE) up -d

logs:
	$(DOCKER_COMPOSE) logs -f

shell:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bash

php-shell:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console

cache-clear:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console cache:clear

db-create:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console doctrine:database:create

db-schema-update:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console doctrine:schema:update --force --complete

db-drop:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console doctrine:database:drop --force

db-seed:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bin/console doctrine:fixtures:load --no-interaction

test:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) ./vendor/bin/phpunit

install:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) composer install

build:
	cd ../frontend/ && yarn build