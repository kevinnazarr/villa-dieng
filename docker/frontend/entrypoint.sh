#!/bin/sh
# Minimal dev entrypoint: ensure dependencies, then start Vite.
set -e

if [ ! -x node_modules/.bin/vite ]; then
  npm install
fi

exec npm run dev -- --host 0.0.0.0 --port 5173
