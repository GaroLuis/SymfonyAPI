s=app
u=www-data

.PHONY: init
init: rm up copy-env install db ## recreate containers and install dependencies
.PHONY: copy-env
copy-env:
	cp --no-clobber .env .env.local
.PHONY: rm
rm: ## stop and delete containers, clean volumes.
	docker-compose stop
	docker-compose rm -v -f
	docker-compose build
.PHONY: stop
stop: ## stop environment
	docker-compose stop
.PHONY: up
up: ## spin up environment
	docker-compose up -d
.PHONY: bash
bash: ## Connect to the development container
	docker-compose exec --user=${u} ${s} /bin/bash
.PHONY: install
install: ## install project dependencies
	docker-compose run --user=${u} --rm ${s} sh -lc 'composer install'
.PHONY: db
db: ## create database, migrations and fixtures
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console doctrine:database:create --if-not-exists'
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console doctrine:migration:migrate --no-interaction'
.PHONY: test
test: ## Run all the tests suites
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console doctrine:database:drop --force  --if-exists --env=test'
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console doctrine:database:create  --if-not-exists --env=test'
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console doctrine:migration:migrate --quiet --no-interaction --env=test'
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/console hautelook:fixtures:load -n --env=test'
	docker-compose run --user=${u} --rm ${s} sh -lc 'bin/phpunit'
.PHONY: logs
logs: ## look for 's' service logs, make s=php logs
	docker-compose logs -f ${s}
