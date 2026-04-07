# Produktivbetrieb: aktueller Stand

## Kurzantwort

- **Ja**, die aktuelle Version ist **technisch lauffähig** und kann in kleinen internen Umgebungen genutzt werden.
- **Nein**, sie ist noch **kein vollständig gehärtetes Enterprise-Produkt**.

## Was mit `compose.prod.yaml` gemeint ist

`compose.prod.yaml` ist eine **härtere Betriebsvariante** im Vergleich zur Dev-Compose:
- ohne Mailpit
- ohne öffentliches DB-Port-Mapping
- `APP_ENV=production` + `APP_DEBUG=false`
- `restart: always`

Sie ist für produktionsnahe Deployments gedacht, ersetzt aber keine vollständige Sicherheits-/Betriebshärtung.

## Was bereits vorhanden ist

- IMAP-Polling + Queue + Scheduler
- Parser-Regeln inkl. Priorität, Test und Trace/Audit
- Verschlüsselte Speicherung von Mailkonto-Passwörtern
- Debug-Ansicht für schnelle Diagnose

## Was vor „echter Produktion“ empfohlen ist

1. Reverse Proxy + TLS (Nginx/Caddy/Traefik)
2. Benutzer-/Rechtesystem aktivieren/härten (2FA, Session-Policies)
3. Backup/Restore-Konzept für DB und Konfiguration
4. Monitoring + Alarmierung (Container/Queue/Fehlerquote)
5. Testabdeckung erhöhen (Unit/Feature + Smoke-Tests)
6. Secrets-Handling verbessern (z. B. Docker secrets/Vault)
7. Mailpit in Produktion nicht betreiben

## Start mit produktiver Compose

```bash
cp .env.prod.example .env.prod
# .env.prod anpassen

docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
```
