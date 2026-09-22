<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\PgTestCase;

class AuthApiTest extends PgTestCase
{
    public function test_register_returns_201_guest_token(): void
    {
        $r = $this->postJson('/api/v1/auth/register', [
            'name' => 'New Guest',
            'email' => 'new@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $r->assertCreated()
            ->assertJsonStructure(['data' => ['user' => ['id', 'email', 'role'], 'token']])
            ->assertJsonPath('data.user.role', 'guest');

        $this->assertSame('guest', User::where('email', 'new@example.com')->first()->role->value);
    }

    public function test_register_ignores_role_input(): void
    {
        $r = $this->postJson('/api/v1/auth/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'role' => 'admin',
        ]);

        $r->assertCreated()->assertJsonPath('data.user.role', 'guest');
        $this->assertSame('guest', User::where('email', 'sneaky@example.com')->first()->role->value);
    }

    public function test_login_success_and_me_resource(): void
    {
        User::factory()->create(['email' => 'member@example.com']);

        $r = $this->postJson('/api/v1/auth/login', [
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $r->assertOk()->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email', 'role'], 'token']]);
        $this->assertArrayNotHasKey('password', $r->json('data.user'));

        $me = $this->getJson('/api/v1/me', ['Authorization' => 'Bearer '.$r->json('data.token')]);
        $me->assertOk()->assertJsonPath('data.email', 'member@example.com');
    }

    public function test_login_invalid_credentials_generic(): void
    {
        User::factory()->create(['email' => 'member@example.com']);

        $this->postJson('/api/v1/auth/login', ['email' => 'member@example.com', 'password' => 'wrong'])
            ->assertUnprocessable();

        $this->postJson('/api/v1/auth/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertUnprocessable();
    }

    public function test_null_password_guest_cannot_login(): void
    {
        User::factory()->create(['email' => 'guest@example.com', 'password' => null]);

        $this->postJson('/api/v1/auth/login', ['email' => 'guest@example.com', 'password' => 'anything'])
            ->assertUnprocessable();
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->postJson('/api/v1/auth/logout', [], ['Authorization' => 'Bearer '.$token])
            ->assertNoContent();

        // Same app instance serves all calls in one test; drop the cached
        // guard user so the next request re-authenticates the (deleted) token.
        auth()->forgetGuards();

        $this->getJson('/api/v1/me', ['Authorization' => 'Bearer '.$token])->assertUnauthorized();
    }

    public function test_me_requires_auth(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_me_returns_user_resource_shape(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/me')->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);
    }
}
