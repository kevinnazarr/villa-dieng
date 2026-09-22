<?php

namespace Tests\Feature\Api;

use Tests\PgTestCase;

class ErrorContractApiTest extends PgTestCase
{
    public function test_401_shape(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_404_shape(): void
    {
        $this->getJson('/api/v1/properties/does-not-exist')->assertNotFound()
            ->assertJson(['message' => 'Not Found.']);
    }

    public function test_422_shape(): void
    {
        $this->postJson('/api/v1/reservations', [])->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_unknown_route_404_json(): void
    {
        $this->getJson('/api/v1/nope')->assertNotFound()
            ->assertJson(['message' => 'Not Found.']);
    }
}
