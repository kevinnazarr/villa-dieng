<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        // Throttle counters persist in Redis across tests in one run.
        // Keys are md5(limiter+key) via the hashed named-limiter path.
        foreach (['api', 'auth-strict', 'booking'] as $limiter) {
            foreach (['api', 'auth', 'booking'] as $prefix) {
                try {
                    \Illuminate\Support\Facades\Redis::connection()->del(
                        md5($limiter.$prefix.':127.0.0.1')
                    );
                } catch (\Throwable) {
                    // Redis unavailable — limiters are inert anyway.
                }
            }
        }

        parent::tearDown();
    }
}
