# backup-monitor

Docker-Startprojekt für den **Backup Mail Monitor** (Laravel-basiert).

## Was bedeutet MVP?

**MVP** = **Minimum Viable Product**.

Einfach gesagt: die **kleinste sinnvolle, lauffähige Version** eines Produkts, mit der man bereits echten Nutzen hat und testen kann, ob die Richtung stimmt.

Für dieses Projekt heißt MVP konkret:
- Mailkonten einrichten
- IMAP pollen
- Rohmails speichern
- erste automatische Zuordnung (vordefinierte Veeam/Backup-Exec-Regeln)
- Events + Status im Dashboard sehen

## Status: Ist das schon lauffähig?

**Ja, als MVP-Entwicklungsstand ist es lauffähig** – mit diesen Grenzen:

- Lauffähig: Dashboard, Mandanten, Mailkonten, Rohmail-Ansicht, IMAP-Poller, vordefinierte Parser-Regeln (Veeam/Backup Exec), Auto-Zuordnung
- Noch nicht final: Regel-Testmodus mit Beispielmail, weitere Herstellerparser, produktive Security-Härtung

## Was ist jetzt konkret der nächste Schritt?

1. Docker-Env vorbereiten
2. Laravel in `./app` erzeugen
3. Overlay einspielen
4. Container starten und Datenbank migrieren/seeden
5. IMAP-Poller + Parser starten und im Browser prüfen

## Quickstart

1. Beispiel-Environment kopieren:
   ```bash
   cp .env.docker.example .env.docker
   ```
2. Laravel-Projekt initialisieren (in `./app`):
   ```bash
   ./scripts/init-laravel.sh
   ```
3. App-Overlay einspielen:
   ```bash
   ./scripts/apply-overlay.sh
   ```
4. Services starten:
   ```bash
   ./scripts/dev-up.sh
   ```
5. Datenbank migrieren + Demo-Daten laden:
   ```bash
   docker compose exec app php artisan migrate --seed
   ```
6. Polling für IMAP-Konten ausführen:
   ```bash
   docker compose exec app php artisan backup-monitor:poll-imap
   ```
7. Parser für Rohmails ausführen:
   ```bash
   docker compose exec app php artisan backup-monitor:parse-raw-emails
   ```
8. Anwendung öffnen:
   - http://localhost:8000

## IMAP Verschlüsselung pro Konto

In der Mailkonto-Verwaltung kann pro Konto individuell `encryption` gewählt werden:

- `ssl` -> IMAP/SSL (`/imap/ssl`, typischerweise Port 993)
- `tls` -> IMAP/TLS (`/imap/tls`, typischerweise Port 143)
- `none` -> IMAP ohne TLS (`/imap/notls`)

Der Poller nutzt diese Einstellung je Konto beim Verbindungsaufbau.

## Enthaltenes Overlay (Module)

- Dashboard mit Computerliste und Ampel-Farben
- Detailseite pro Computer mit Aliasen + Event-Historie
- Mandanten-Verwaltung (Liste/Anlegen/Bearbeiten)
- Mailkonten-Verwaltung (Liste/Anlegen/Bearbeiten)
- Rohmail-Liste + Rohmail-Detailansicht
- IMAP-Poller (Command + Queue Job)
- Parser-Pipeline (vordefinierte Regeln + eigene Regeln, automatische Event-/Computer-Zuordnung)
- Parser Trace / Audit pro Rohmail
- sichere Passwortspeicherung für Mailkonten (verschlüsselt)
- Debug-Ansicht für Systemstatus und Fehler
- vorbereitete parser_rules-Basis für eigene Regeln
- Migrations + Demo-Seeder

## Deployment

- Docker-Host Anleitung: `docs/DOCKERHOST_DEPLOY.md`
- Produktive Betriebsnotizen: `docs/PRODUCTION_READINESS.md`

### Produktive Compose starten

```bash
cp .env.prod.example .env.prod
# .env.prod anpassen
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
```

## Hinweise

- Primärer Entwicklungsmodus ist Docker Compose.
- Native VM-Ausführung bleibt möglich, ist aber nicht der Standardpfad.
- Das Overlay ist bewusst ein MVP-Startpunkt und wird schrittweise erweitert.


## Eigene Parser / Regeln

Ja, deine Annahme ist richtig: es gibt jetzt eine WebGUI für `parser_rules` (anlegen/bearbeiten/löschen).

Aktuell:
- vordefinierte Regeln für Veeam und Backup Exec (als Startvorlage)
- zusätzlich konfigurierbare Regeln über die Oberfläche (`parser_rules`)

Wichtig:
- Veeam ist nicht mehr zwingend hardcoded, sondern als Regelset vordefiniert
- du kannst Regeln duplizieren, bearbeiten, deaktivieren und löschen
- Regeln haben jetzt Priorität (kleiner = früher) und können in der Liste hoch/runter geschoben werden
- Reihenfolge aktuell: parser_rules -> fallback unmatched

Nächster Ausbauschritt:
- Versions-/Audit-Historie für Parser-Regeln


## Security-Hinweis Mailpasswörter

Mailpasswörter werden beim Speichern verschlüsselt abgelegt und zur Laufzeit nur für den Verbindungsaufbau entschlüsselt.
