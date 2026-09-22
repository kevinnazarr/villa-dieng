-- Required by docs/Technical-Design-Villa-Dieng.md.
-- Runs once on first postgres volume init (see README database reset).
CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS btree_gist;
CREATE EXTENSION IF NOT EXISTS citext;
