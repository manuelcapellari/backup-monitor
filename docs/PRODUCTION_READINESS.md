# Produktivbetrieb: aktueller Stand

## Kurzantwort


- **Ja**, die aktuelle Version ist **technisch lauffähig** und kann in internen Umgebungen produktiv genutzt werden.
- **Nein**, sie ist noch **kein vollständig gehärtetes Enterprise-Produkt** (z. B. Compliance/HA/umfangreiche Tests).
- **Ja**, die aktuelle Version ist **technisch lauffähig** und kann in kleinen internen Umgebungen genutzt werden.
- **Nein**, sie ist noch **kein vollständig gehärtetes Enterprise-Produkt**.


## Was mit `compose.prod.yaml` gemeint ist

`compose.prod.yaml` ist eine **härtere Betriebsvariante** im Vergleich zur Dev-Compose:
- ohne Mailpit
- ohne öffentliches DB-Port-Mapping
- `APP_ENV=production` + `APP_DEBUG=false`
- `restart: always`

Für euren rein internen Einsatz ist das ein passender Startpunkt.

Sie ist für produktionsnahe Deployments gedacht, ersetzt aber keine vollständige Sicherheits-/Betriebshärtung.

## Was bereits vorhanden ist

- IMAP-Polling + Queue + Scheduler
- Parser-Regeln inkl. Priorität, Test und Trace/Audit
- Verschlüsselte Speicherung von Mailkonto-Passwörtern
- Debug-Ansicht für schnelle Diagnose
- Optionaler interner HTTP-Basic-Auth-Schutz via Env (`INTERNAL_AUTH_USER`, `INTERNAL_AUTH_PASSWORD`)

## Was vor „größerem Rollout“ empfohlen ist

1. Backup/Restore-Konzept für DB und Konfiguration
2. Monitoring + Alarmierung (Container/Queue/Fehlerquote)
3. Testabdeckung erhöhen (Unit/Feature + Smoke-Tests)
4. Secrets-Handling verbessern (z. B. Docker secrets/Vault)
5. Rollen/Rechte und Audit-Policies feinziehen
=======

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
# optional interner Zugriffsschutz setzen:
# INTERNAL_AUTH_USER=admin
# INTERNAL_AUTH_PASSWORD=<starkes_passwort>

docker compose -f compose.prod.yaml -f compose.data-local.yaml up -d --build
docker compose -f compose.prod.yaml -f compose.data-local.yaml exec app php artisan migrate --force
```


Hinweis: `compose.data-local.yaml` bindet MySQL-Daten unter `./data/mysql` ein.
=======

docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
```
