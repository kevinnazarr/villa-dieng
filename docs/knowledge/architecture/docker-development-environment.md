# Local Docker Development Environment

## Services

`docker-compose.yml` defines four development services:

- `postgres` — `postgres:15-alpine`, authoritative database, named volume `pgdata`, healthcheck via `pg_isready`.
- `redis` — `redis:7-alpine`, support service (cache/queue/session), healthcheck via `redis-cli ping`. Never the availability source of truth.
- `backend` — built from `docker/php/Dockerfile` (`php:8.4-cli-alpine` + `pdo_pgsql` + `phpredis`), runs `php artisan serve` on container port 8000. Waits for postgres + redis healthy. Bind-mounts `./backend`. Env forces `CACHE_STORE`/`QUEUE_CONNECTION`/`SESSION_DRIVER=redis`, `DB_HOST=postgres`, `REDIS_HOST=redis`.
- `frontend` — built from `docker/frontend/Dockerfile` (`node:22-slim`), runs Vite dev on 5173. Bind-mounts `./frontend`. `VITE_API_URL` points at the backend host port.

## Decisions

- Host ports avoid common conflicts: redis `6380` (host redis often holds 6379), backend `8001` (portainer often holds 8000). All ports overridable via root `.env` (see `.env.example`); container-internal ports stay standard.
- Backend image is PHP 8.4 because installed vendor dependencies require `>= 8.4.1` (platform check fails on 8.3).
- Frontend image is Debian-based `node:22-slim`, not Alpine: host `node_modules` is bind-mounted and its rolldown native binding is glibc-built, which breaks under musl.
- Frontend entrypoint reinstalls only when `node_modules/.bin/vite` is missing; no anonymous `node_modules` volume, so the container uses host-installed deps.
- PostgreSQL extensions (`pgcrypto`, `btree_gist`, `citext`) are enabled via `docker/postgres/init-extensions.sql`, mounted into `docker-entrypoint-initdb.d`. Runs only on first volume init.
- Redis is wired as cache/queue/session store for dev parity, but booking availability authority stays PostgreSQL per the technical design.

## Gotchas

- `init-extensions.sql` does not re-run on existing volumes — reset with `docker compose down -v` to re-bootstrap.
- Backend entrypoint generates `APP_KEY` only when the env var is absent; real Compose env vars take precedence over `backend/.env`.
- Container-internal service names (`postgres`, `redis`) are used for inter-service communication; host ports are for local access only.
