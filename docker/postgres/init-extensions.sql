-- Villa Dieng local development bootstrap.
-- Enables the PostgreSQL extensions required by docs/Technical-Design-Villa-Dieng.md.
-- Runs automatically on first volume initialization (docker-entrypoint-initdb.d).

CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS btree_gist;
CREATE EXTENSION IF NOT EXISTS citext;
