.PHONY: up build-up down down-volumes api-test builder-prune bootstrap

up:
	docker compose up -d --wait

bootstrap:
	node bootstrap.mjs

build-up: bootstrap
	docker compose up -d --build --force-recreate --wait

down:
	docker compose down

down-volumes:
	docker compose down -v

api-test:
	docker compose exec api php artisan test

builder-prune:
	docker builder prune -af
