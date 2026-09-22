<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * Base class for PostgreSQL-backed integration tests.
 *
 * Fail-hard: PG constraint behavior (EXCLUDE, CITEXT, partial uniques)
 * cannot be verified on sqlite, so a missing PG test database is a
 * hard failure, never a skip.
 */
abstract class PgTestCase extends TestCase
{
    use RefreshDatabase;

    protected function refreshApplication(): void
    {
        putenv('DB_CONNECTION=pgsql_testing');
        $_ENV['DB_CONNECTION'] = 'pgsql_testing';
        $_SERVER['DB_CONNECTION'] = 'pgsql_testing';

        parent::refreshApplication();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Verify connectivity; fail-hard on no PG.
        try {
            DB::connection('pgsql_testing')->getPdo();
        } catch (\Throwable $e) {
            $this->fail('PostgreSQL test database unavailable: '.$e->getMessage());
        }

        // EXCLUDE constraints need btree_gist; CITEXT columns need citext.
        // The dev DB gets these via docker/postgres/init-extensions.sql;
        // the test DB is created ad-hoc so ensure them here (superuser).
        DB::connection('pgsql_testing')->statement('CREATE EXTENSION IF NOT EXISTS btree_gist');
        DB::connection('pgsql_testing')->statement('CREATE EXTENSION IF NOT EXISTS pgcrypto');
        DB::connection('pgsql_testing')->statement('CREATE EXTENSION IF NOT EXISTS citext');
    }
}
