#!/usr/bin/env bash
set -euo pipefail

if [ ! -f app/artisan ]; then
  mkdir -p app
  docker compose run --rm app composer create-project laravel/laravel . --prefer-dist --no-interaction
fi

if [ ! -f .env.docker ]; then
  cp .env.docker.example .env.docker
fi

cp .env.docker app/.env

docker compose run --rm app php artisan key:generate

docker compose run --rm app php artisan migrate

echo "Laravel wurde initialisiert. Starte mit: docker compose up -d"
