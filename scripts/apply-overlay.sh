#!/usr/bin/env bash
set -euo pipefail

if [ ! -f app/artisan ]; then
  echo "Kein Laravel-Projekt in ./app gefunden. Bitte zuerst scripts/init-laravel.sh ausführen."
  exit 1
fi

if command -v rsync >/dev/null 2>&1; then
  rsync -av overlay/ app/
else
  cp -R overlay/. app/
fi

echo "Overlay wurde nach ./app kopiert."
echo "Nächste Schritte:"
echo "  1) docker compose up -d --build"
echo "  2) docker compose exec app php artisan migrate --seed"
echo "  3) docker compose exec app php artisan backup-monitor:poll-imap"
echo "  4) docker compose exec app php artisan backup-monitor:parse-raw-emails"
echo "  5) docker compose exec app php artisan route:list"
