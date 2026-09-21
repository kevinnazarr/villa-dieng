## Villa Dieng — Cabin Booking Engine

Local Docker development foundation for the Laravel API + React/Vite frontend + PostgreSQL + Redis stack.

### Prerequisites

- Docker + Docker Compose
- Ports free: `5432` (postgres), `6380` (redis), `8001` (backend), `5173` (frontend)

### Start

```bash
cp .env.example .env   # optional, defaults already work
docker compose up -d --build
```

### Stop

```bash
docker compose stop     # keep data
docker compose down     # stop + remove containers (keeps pgdata volume)
```

### Inspect

```bash
docker compose ps -a
docker compose logs -f backend
docker compose logs -f frontend
docker compose logs -f postgres
docker compose logs -f redis
```

### App commands

```bash
docker exec villa_dieng_backend php artisan migrate
docker exec villa_dieng_backend php artisan tinker
```

### Reset database

```bash
docker compose down -v   # WARNING: deletes pgdata volume
docker compose up -d
```

### Ports

| Service  | Host port | Container |
|----------|-----------|-----------|
| postgres | 5432      | 5432      |
| redis    | 6380      | 6379      |
| backend  | 8001      | 8000      |
| frontend | 5173      | 5173      |

### Env config

- `.env.example` → copy to `.env`, compose-only (ports, postgres credentials). `.env` is gitignored, never commit secrets.
- `backend/.env` → Laravel runtime config (untracked, already exists locally).

### Troubleshooting

- `address already in use` on redis/backend: host already uses 6379/8000 — this repo defaults to 6380/8001 to avoid that. Override via `.env` (`REDIS_PORT`, `APP_PORT`).
- `vite: not found`: entrypoint runs `npm install` when `node_modules/.bin/vite` is missing; if native binding errors appear, remove host `frontend/node_modules` and let the container reinstall.
- `Your Composer dependencies require PHP >= 8.4`: backend image is `php:8.4-cli-alpine` — rebuild with `docker compose build backend` if stale.
- Postgres extensions missing: `docker/postgres/init-extensions.sql` runs only on first volume init — `down -v` + `up` re-runs it.
