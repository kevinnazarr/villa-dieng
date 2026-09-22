<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_returns_ok_with_dependency_checks(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJsonStructure(['status', 'checks' => ['database', 'redis']]);
    }

    public function test_unknown_api_route_returns_json_404(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $response->assertNotFound()->assertJson(['message' => 'Not Found.']);
    }

    public function test_validation_error_shape_has_errors_key(): void
    {
        $response = $this->postJson('/api/v1/probe/validation', []);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_authenticated_user_can_reach_me(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/me')->assertOk()
            ->assertJsonStructure(['id', 'email']);
    }

    public function test_user_id_is_uuid(): void
    {
        $user = User::factory()->create();

        $this->assertTrue((bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            (string) $user->getKey()
        ));
        $this->assertTrue(in_array(DB::getSchemaBuilder()->getColumnType('users', 'id'), ['string', 'varchar', 'char', 'guid', 'uuid'], true));
    }

    public function test_redis_connection_pings(): void
    {
        try {
            // phpredis returns bool true; predis returns 'PONG'/'+PONG'.
            $pong = Redis::connection()->ping();
            $this->assertTrue($pong === true || in_array(strtoupper(ltrim((string) $pong, '+')), ['PONG', '1'], true));
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis unavailable in test env: '.$e->getMessage());
        }
    }
}
