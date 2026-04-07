#!/usr/bin/env bash
set -euo pipefail

if [ ! -f .env.docker ]; then
  cp .env.docker.example .env.docker
fi

if [ ! -f app/artisan ]; then
  echo "Kein Laravel-Projekt in ./app gefunden. Bitte zuerst scripts/init-laravel.sh ausführen."
  exit 1
fi

cp .env.docker app/.env

docker compose up -d --build

echo "App: http://localhost:8000"
echo "Mailpit: http://localhost:8025"
