DOCKER_COMPOSE = docker compose -f docker-compose.yaml
DOCKER_RUN = docker compose run

all:
	@echo "Docker: "
	@echo " - make up"
	@echo " - make build"
	@echo " - male down"
	@echo " - make run"
	@echo " - make restart"
	@echo "Composer: "
	@echo " - make composer/install"
	@echo " - make composer/require"
	@echo " - make composer/require-dev"
	@echo " - make composer/dump-autoload"
	@echo " - make composer/remove"
	@echo "Artisan: "
	@echo " - make artisan/make"
	@echo " - make artisan/key-generate"
	@echo " - make artisan/migrate"
	@echo " - make artisan/migrate-refresh"
	@echo " - make artisan/dispatch"
	@echo "npm: "
	@echo " - make npm/install"
	@echo " - make npm/build"

composer/install:
	${DOCKER_RUN} composer install

composer/require:
	${DOCKER_RUN} composer require $(REQ)

composer/require-dev:
	${DOCKER_RUN} composer require --dev $(REQ)

composer/dump-autoload:
	${DOCKER_RUN} composer dump-autoload

composer/remove:
	${DOCKER_RUN} composer remove $(REM)

artisan/make:
	${DOCKER_RUN} artisan make:$(ENT) $(NAME) $(FLAGS)

artisan/route_list:
	${DOCKER_RUN} artisan route:list

artisan/key-generate:
	${DOCKER_RUN} artisan key:generate

artisan/migrate:
	${DOCKER_RUN} artisan migrate

artisan/migrate-refresh:
	${DOCKER_RUN} artisan migrate:refresh

artisan/dispatch:
	${DOCKER_RUN} artisan app:dispatch_products

artisan/run-list: artisan/key-generate artisan/migrate

npm/install:
	${DOCKER_RUN} npm install

npm/build:
	${DOCKER_RUN} npm run build

run: build up composer/install npm/install npm/build artisan/run-list

build:
	${DOCKER_COMPOSE} build

up:
	${DOCKER_COMPOSE} up -d

down:
	${DOCKER_COMPOSE} down

restart:
	${DOCKER_COMPOSE} restart