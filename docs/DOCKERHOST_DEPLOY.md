# Deployment auf einem Docker-Host (Schritt für Schritt)

## Voraussetzungen auf dem Host

- Linux-Server mit SSH-Zugang
- Docker Engine + Docker Compose Plugin installiert
- Git installiert
- Ports erreichbar:
  - `8000` (App)
  - `3307` (MySQL optional extern)
  - `8025` (Mailpit, nur für Dev/Test)

## 1) Projekt holen

```bash
git clone <DEIN_REPO_URL> backup-monitor
cd backup-monitor
```


## Alternative: Komplett-Installer

```bash
./scripts/full-install.sh <DEIN_REPO_URL> [branch] [target_dir]
```

Der Installer richtet automatisch alles ein und speichert DB-Daten in `./data/mysql`.



## 2) Env-Datei anlegen

```bash
cp .env.docker.example .env.docker
```

Danach `.env.docker` anpassen (wichtig):
- `APP_ENV=production` (oder `staging`)
- `APP_DEBUG=false`
- DB-Werte prüfen
- Mail-Werte prüfen
- optional interner Zugriffsschutz setzen: `INTERNAL_AUTH_USER` / `INTERNAL_AUTH_PASSWORD`



- optional interner Zugriffsschutz setzen: `INTERNAL_AUTH_USER` / `INTERNAL_AUTH_PASSWORD`


## 3) Laravel-App (im `app/`-Ordner) initialisieren

```bash
./scripts/init-laravel.sh
./scripts/apply-overlay.sh
```

## 4) Container starten

```bash
./scripts/dev-up.sh
```

Das startet:
- `app` (Laravel Web)
- `worker` (Queue)
- `scheduler` (Cron/Schedule)
- `mysql`
- `mailpit`

## 5) Datenbank migrieren + Demo-Daten

```bash
docker compose exec app php artisan migrate --seed
```

## 6) Funktionscheck

```bash
docker compose ps
docker compose logs -f app
docker compose logs -f worker
docker compose logs -f scheduler
```

Web öffnen:
- `http://<HOST-IP>:8000`
- Debug: `http://<HOST-IP>:8000/debug`

## 7) IMAP-Poller + Parser manuell testen

```bash
docker compose exec app php artisan backup-monitor:poll-imap
docker compose exec app php artisan backup-monitor:parse-raw-emails
```

## 8) Betrieb / Updates

Update aus Git:

```bash
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

## 9) Häufige Fehler

### `docker: command not found`
Docker/Compose ist nicht installiert oder nicht im PATH.

### IMAP-Fehler trotz korrekter Zugangsdaten
- Prüfe `encryption` + Port im Mailkonto (`ssl`/`tls`/`none`).
- Prüfe Firewall/Netzwerk vom Host zum Mailserver.

### App startet, aber 500-Fehler
- Logs prüfen: `docker compose logs -f app`
- APP_KEY prüfen (wird in `init-laravel.sh` erzeugt)
- Migrationen prüfen

## 10) Für echten Produktivbetrieb (Empfehlung)

Für rein internen Betrieb ist kein Reverse Proxy zwingend nötig.



Für rein internen Betrieb ist kein Reverse Proxy zwingend nötig.


- Reverse Proxy (Nginx/Caddy/Traefik) vor Port 8000
- TLS-Zertifikat (Let's Encrypt)

- Mailpit deaktivieren
- DB-Port nicht öffentlich exponieren
- Backups für DB-Volume
- Monitoring/Alerting ergänzen


## Optional: produktionsnahe Compose-Variante

```bash
cp .env.prod.example .env.prod
# .env.prod anpassen
docker compose -f compose.prod.yaml -f compose.data-local.yaml up -d --build
docker compose -f compose.prod.yaml -f compose.data-local.yaml exec app php artisan migrate --force


docker compose -f compose.prod.yaml -f compose.data-local.yaml up -d --build
docker compose -f compose.prod.yaml -f compose.data-local.yaml exec app php artisan migrate --force

docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force

```

Details: `docs/PRODUCTION_READINESS.md`
