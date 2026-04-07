#!/usr/bin/env bash
set -euo pipefail

echo "=== Merge-Konflikt Diagnose ==="

printf "\n1) Unmerged Dateien (laut Git):\n"
git diff --name-only --diff-filter=U || true

printf "\n2) Dateien mit Konfliktmarkern (<<<<<<<, =======, >>>>>>>):\n"
rg -n "^(<<<<<<<|=======|>>>>>>>)" . || true

printf "\n3) Nächste Schritte:\n"
echo "  a) Datei öffnen und zwischen den Blöcken entscheiden"
echo "  b) Konfliktmarker entfernen"
echo "  c) Datei als gelöst markieren: git add <datei>"
echo "  d) Prüfen, ob noch Konflikte offen sind: git diff --name-only --diff-filter=U"
echo "  e) Danach Commit: git commit"
