DOCKER_COMPOSE = docker compose -f docker-compose.yaml
DOCKER_RUN = docker compose run

all:
	@echo "Docker: "
	@echo " - make up"
	@echo " - make build"
	@echo " - male down"
	@echo " - make run"
	@echo " - make restart"
	@echo "   Exec:"
	@echo "    - make exec/app_bash"
	@echo "Composer: "
	@echo " - make composer/install"
	@echo " - make composer/update"
	@echo " - make composer/require"
	@echo " - make composer/require-dev"
	@echo " - make composer/dump-autoload"
	@echo " - make composer/remove"
	@echo "Artisan: "
	@echo " - make artisan/key-generate"
	@echo " - make artisan/migrate"
	@echo " - make artisan/seed"
	@echo " - make artisan/migrate-refresh"
	@echo " - make artisan/migrate-seed"
	@echo " - make artisan/storage-link"
	@echo "npm: "
	@echo " - make npm/install"
	@echo " - make npm/build"
	@echo " - make npm/dev"

composer/install:
	${DOCKER_RUN} composer install

composer/update:
	${DOCKER_RUN} composer update

composer/require:
	${DOCKER_RUN} composer require $(REQ)

composer/require-dev:
	${DOCKER_RUN} composer require --dev $(REQ)

composer/dump-autoload:
	${DOCKER_RUN} composer dump-autoload

composer/remove:
	${DOCKER_RUN} composer remove $(FOO)

artisan/key-generate:
	${DOCKER_RUN} artisan key:generate

artisan/migrate:
	${DOCKER_RUN} artisan migrate

artisan/seed:
	${DOCKER_RUN} artisan db:seed

artisan/migrate-refresh:
	${DOCKER_RUN} artisan migrate:refresh

artisan/migrate-seed:
	${DOCKER_RUN} artisan migrate --seed

artisan/storage-link:
	${DOCKER_RUN} artisan storage:link

artisan/run-list: artisan/key-generate artisan/migrate

npm/install:
	${DOCKER_RUN} npm install

npm/build:
	${DOCKER_RUN} npm run build

npm/dev:
	${DOCKER_RUN} npm run dev

run: build up composer/install npm/install npm/build artisan/run-list

build:
	${DOCKER_COMPOSE} build

up:
	${DOCKER_COMPOSE} up -d

down:
	${DOCKER_COMPOSE} down

restart:
	${DOCKER_COMPOSE} restart

exec/app_bash:
	docker exec -it php /bin/bash