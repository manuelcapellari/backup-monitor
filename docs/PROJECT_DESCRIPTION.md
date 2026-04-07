# Projektbeschreibung: Backup Mail Monitor

## 1) Zielbild
Der **Backup Mail Monitor** ist ein mandantenfähiges Webtool, das Status-E-Mails von Backup-Lösungen (z. B. Veeam, Backup Exec) über IMAP/POP3 abruft, normalisiert und in einem Dashboard visualisiert.

## 2) Kernziele
- Übersichtliches Dashboard mit Computerliste und Ampelstatus (grün/gelb/rot/grau).
- Detailseite pro Computer mit Verlauf der Sicherungsergebnisse.
- Alias-Verwaltung für technische Hostnamen.
- Mandantenfähigkeit mit automatischer und manueller Zuordnung.
- Mehrere Mailkonten (IMAP-first, POP3 als Fallback).
- Super-Admin-Modus für zentrale Sicht/Bearbeitung über alle Mandanten.
- Vollständige Konfiguration über WebGUI.
- "Anlernen" von Sicherungsmails über regelbasiertes Parsing.
- Nicht zuordenbare Mails in eigene Kategorie.
- **Originalmail-Ansicht** (Header + Text/HTML + Metadaten) für Nachvollziehbarkeit.

## 3) Nicht-Ziele für MVP
- Kein komplexes Multi-Role-Rechtesystem (nur Super-Admin + optional Admin).
- Kein KI-First Parsing.
- Kein externes SIEM/BI in V1.

## 4) Architektur (MVP)
- **Backend/GUI:** Laravel (Blade, optional später Livewire).
- **DB:** MySQL 8.x.
- **Verarbeitung:** Queue-Jobs + Scheduler.
- **Mail-Ingestion:** IMAP zuerst, POP3 Fallback-Adapter.
- **Betrieb:** Docker Compose (app, worker, scheduler, mysql, mailpit).

## 5) Fachmodule
1. Auth & Security
   - Login, Forgot Password, E-Mail-Verifikation.
   - 2FA via TOTP + optional E-Mail-OTP.
2. Tenant Management
   - Tenants, Zuordnungsregeln, manuelle Overrides.
3. Mail Accounts
   - Mehrere Konten, Polling-Intervall, Protokoll.
4. Mail Processing
   - Rohmail speichern, deduplizieren, klassifizieren.
5. Parser Engine
   - Parserprofile pro Hersteller (Veeam, Backup Exec).
6. Asset/Computer View
   - Host-Auflösung, Aliasverwaltung, Statusaggregation.
7. Dashboard & Detail
   - Aktueller Status + Historie + Originalmail-Link.

## 6) Kategorien / Status
- `backup_success`
- `backup_warning`
- `backup_error`
- `backup_info`
- `other_unmatched`

Ampel-Regeln (initial):
- Grün: letzter Lauf erfolgreich und aktuell
- Gelb: letzter Lauf Warnung oder Stale-Grenze überschritten
- Rot: letzter Lauf fehlerhaft
- Grau: keine verwertbaren Daten

## 7) Datenmodell (vorgeschlagen)
- `users`
- `tenants`
- `mail_accounts`
- `tenant_assignment_rules`
- `computers`
- `computer_aliases`
- `raw_emails`
- `email_classifications`
- `backup_events`
- `parser_profiles`
- `parser_rules`
- `manual_overrides`
- `audit_logs`

## 8) Originalmail-Einsicht (verbindlich)
- Speicherung von RFC822-Rohdaten (oder sicherer Referenz).
- Anzeige von:
  - Headern (From, To, Subject, Date, Message-ID)
  - Plain-Text Body
  - HTML Body (sicher gerendert/sanitized)
  - Attachments-Metadaten
- Verlinkung von Backup-Event -> zugehörige Originalmail.
- Rollen-/Mandantenprüfung für Einsicht.

## 9) Entwicklungspfad
### Phase 1 (MVP)
- Auth + Super-Admin
- Mailkonto-CRUD
- Ingestion-Job (IMAP)
- Klassifikation Basisregeln
- Dashboard + Computerdetail + Originalmailansicht

### Phase 2
- Regel-Editor (Anlernen)
- Reprocessing alter Rohmails
- Dedup robust
- POP3 Adapter

### Phase 3
- Tenant-Admins
- Benachrichtigungen
- Reports/Exports
- Monitoring/Alerting

## 10) Betrieb: zweigleisig
- Primär: Docker Compose.
- Sekundär: Native VM bleibt möglich (Laravel-Standard), aber nicht der Hauptpfad.

## 11) Akzeptanzkriterien MVP
- Mindestens ein IMAP-Konto kann abgefragt werden.
- Verarbeitete Mails erscheinen als normalisierte Backup-Events.
- Dashboard zeigt letzten Status je Computer.
- Detailseite zeigt Historie + verlinkte Originalmail.
- Nicht zuordenbare Mails sind separat sichtbar.
