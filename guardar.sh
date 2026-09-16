#!/usr/bin/env bash
# Guarda y sube todo lo que tengas trabajado en la rama actual.
# Uso: ./guardar.sh "mensaje opcional"

set -e

rama=$(git rev-parse --abbrev-ref HEAD)
mensaje="${1:-WIP $(date '+%Y-%m-%d %H:%M')}"

echo "Rama actual: $rama"

if [ -z "$(git status --porcelain)" ]; then
    echo "No hay cambios para guardar."
    exit 0
fi

git add -A
git commit -m "$mensaje"
git push -u origin "$rama"

echo "Listo. Guardado y subido a origin/$rama."
