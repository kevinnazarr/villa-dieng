#!/bin/sh
# Minimal dev entrypoint: ensure dependencies, then serve.
# Real environment variables (from Compose) take precedence over /app/.env.
set -e

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

exec php artisan serve --host=0.0.0.0 --port=8000
