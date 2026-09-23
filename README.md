## Villa Dieng

Booking engine for an exclusive cabin-style villa in Dieng, Wonosobo: Laravel API backend + React/Vite frontend.

## Run with Docker (development)

Prerequisites: Docker 24+ with Compose v2.

```bash
cp .env.example .env   # optional; overrides ports/credentials
docker compose up --build
```

Services:

| Service  | URL                   | Notes                                      |
|----------|-----------------------|--------------------------------------------|
| frontend | http://localhost:5173 | Vite dev server                            |
| backend  | http://localhost:8000 | Laravel dev server                         |
| postgres | localhost:5432        | user/db per `.env` (`db_villa_dieng` default) |
| redis    | localhost:6379        | cache / queue                              |

PostgreSQL ships with `pgcrypto`, `btree_gist`, `citext` extensions enabled.

Useful commands:

```bash
docker compose up --build        # start (rebuild images)
docker compose up -d             # start in background
docker compose down              # stop (keeps data volumes)
docker compose down -v           # stop AND delete data volumes
docker compose logs -f backend   # follow backend logs
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan test
```

Reset the database (deletes all data):

```bash
docker compose down -v
docker compose up --build
docker compose exec backend php artisan migrate --force
```

Environment: copy root `.env.example` to `.env` to override ports/credentials. Never commit real secrets; `.env` files are gitignored. If host ports 5432/6379/8000/5173 are taken, set `POSTGRES_PORT`, `REDIS_PORT`, `BACKEND_PORT`, `FRONTEND_PORT` in root `.env`.

Troubleshooting:

- `port is already allocated` → something on the host uses that port; override via root `.env` (e.g. `BACKEND_PORT=8001`).
- Backend 500 about DB connection → check `docker compose logs backend`; ensure `postgres` is healthy (`docker compose ps`).
- Frontend blank / connection refused → ensure port 5173 is free and `docker compose logs frontend` shows "ready".