#!/bin/bash
set -euo pipefail

ROOT="/home/u715729948/domains/serutrainingcourse.co.uk/public_html"
REPO_URL="https://github.com/russkinbright/serutrainingcourse.co.uk.git"
BRANCH="main"
LOG="/home/u715729948/domains/serutrainingcourse.co.uk/deploy.log"
LOCK="$ROOT/.deploy_lock"

umask 022
mkdir -p "$(dirname "$LOG")"

# prevent overlap
if ! mkdir "$LOCK" 2>/dev/null; then
  echo "$(date '+%F %T') deploy: another run in progress, skip" >> "$LOG"
  exit 0
fi
trap 'rmdir "$LOCK"' EXIT

echo "$(date '+%F %T') deploy: start" >> "$LOG"

# clone if missing
if [ ! -d "$ROOT/.git" ]; then
  echo "$(date '+%F %T') deploy: cloning repo into $ROOT" >> "$LOG"
  rm -rf "$ROOT"/*
  git clone "$REPO_URL" "$ROOT"
fi

cd "$ROOT"
git config --global --add safe.directory "$ROOT" || true
git remote set-url origin "$REPO_URL" || true
git fetch origin "$BRANCH" --prune

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse "origin/$BRANCH" || echo "")

if [ -n "$REMOTE" ] && [ "$LOCAL" != "$REMOTE" ]; then
  echo "$(date '+%F %T') deploy: updating to origin/$BRANCH" >> "$LOG"
  git reset --hard "origin/$BRANCH"

  # PHP/Laravel post-steps (run only if present)
  if [ -f composer.json ] && command -v composer >/dev/null 2>&1; then
    composer install --no-dev --prefer-dist --optimize-autoloader >> "$LOG" 2>&1 || true
  fi
  if [ -f artisan ]; then
    php artisan config:cache >> "$LOG" 2>&1 || true
    php artisan route:cache  >> "$LOG" 2>&1 || true
    php artisan view:cache   >> "$LOG" 2>&1 || true
    # php artisan migrate --force >> "$LOG" 2>&1 || true   # enable if you want auto migrations
  fi

  echo "$(date '+%F %T') deploy: done (updated)" >> "$LOG"
else
  echo "$(date '+%F %T') deploy: no changes" >> "$LOG"
fi
