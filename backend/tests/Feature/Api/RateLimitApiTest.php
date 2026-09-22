<?php

namespace Tests\Feature\Api;

use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Tests\PgTestCase;

class RateLimitApiTest extends PgTestCase
{
    public function test_auth_limiter_429(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'y']);
        }

        $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'y'])
            ->assertStatus(429);
    }

    public function test_throttle_uses_redis_store(): void
    {
        // throttleWithRedis swaps the alias to the Redis middleware
        // (phpunit forces CACHE_STORE=array; compose pins CACHE_STORE=file —
        // verified in LOG-005, not here).
        $this->assertSame(
            ThrottleRequestsWithRedis::class,
            app('router')->getMiddleware()['throttle']
        );

        $this->assertNotFalse(Redis::connection()->ping());
    }
}
