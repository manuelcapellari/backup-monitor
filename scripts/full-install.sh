#!/usr/bin/env bash
set -euo pipefail

REPO_URL="${1:-}"
BRANCH="${2:-main}"
TARGET_DIR="${3:-$PWD/backup-monitor}"

if [[ -z "$REPO_URL" ]]; then
  echo "Usage: $0 <git_repo_url> [branch] [target_dir]"
  echo "Example: $0 https://github.com/example/backup-monitor.git main ./backup-monitor"
  exit 1
fi

command -v git >/dev/null 2>&1 || { echo "git ist nicht installiert"; exit 1; }
command -v docker >/dev/null 2>&1 || { echo "docker ist nicht installiert"; exit 1; }

echo "[1/8] Clone Repository..."
if [[ -d "$TARGET_DIR/.git" ]]; then
  echo "Repository existiert bereits in $TARGET_DIR - führe git pull aus"
  git -C "$TARGET_DIR" pull --ff-only
else
  git clone --branch "$BRANCH" "$REPO_URL" "$TARGET_DIR"
fi

cd "$TARGET_DIR"

echo "[2/8] .env.prod vorbereiten..."
if [[ ! -f .env.prod ]]; then
  cp .env.prod.example .env.prod
fi

echo "[3/8] Datenverzeichnis in ./data anlegen..."
mkdir -p ./data/mysql

echo "[4/8] Laravel App initialisieren..."
./scripts/init-laravel.sh

echo "[5/8] Overlay anwenden..."
./scripts/apply-overlay.sh

echo "[6/8] Container bauen & starten (prod + local data)..."
docker compose -f compose.prod.yaml -f compose.data-local.yaml up -d --build

echo "[7/8] Migrationen ausführen..."
docker compose -f compose.prod.yaml -f compose.data-local.yaml exec app php artisan migrate --force

echo "[8/8] Optional Demo-Daten laden..."
read -r -p "Demo-Daten laden? (y/N): " LOAD_DEMO
if [[ "${LOAD_DEMO:-N}" =~ ^[Yy]$ ]]; then
  docker compose -f compose.prod.yaml -f compose.data-local.yaml exec app php artisan db:seed --force
fi

echo "Fertig."
echo "App: http://$(hostname -I | awk '{print $1}'):8000"
echo "Daten liegen in: $TARGET_DIR/data/mysql"
