<?php

namespace Tests\Feature\Api;

use App\Models\Cabin;
use App\Models\Property;
use Tests\PgTestCase;

class CatalogApiTest extends PgTestCase
{
    public function test_property_list_hides_inactive(): void
    {
        Property::factory()->create(['slug' => 'active-villa', 'is_active' => true]);
        Property::factory()->create(['slug' => 'closed-villa', 'is_active' => false]);

        $r = $this->getJson('/api/v1/properties');
        $r->assertOk();
        $slugs = collect($r->json('data'))->pluck('slug')->all();
        $this->assertContains('active-villa', $slugs);
        $this->assertNotContains('closed-villa', $slugs);
    }

    public function test_property_detail_and_404(): void
    {
        Property::factory()->create(['slug' => 'pine-estate']);

        $this->getJson('/api/v1/properties/pine-estate')->assertOk()
            ->assertJsonPath('data.slug', 'pine-estate');
        $this->getJson('/api/v1/properties/nope')->assertNotFound()
            ->assertJson(['message' => 'Not Found.']);
    }

    public function test_nested_cabin_list_and_detail(): void
    {
        $prop = Property::factory()->create(['slug' => 'pine-estate']);
        Cabin::factory()->create(['property_id' => $prop->id, 'slug' => 'lodge-a', 'is_active' => true]);
        Cabin::factory()->create(['property_id' => $prop->id, 'slug' => 'lodge-b', 'is_active' => false]);

        $list = $this->getJson('/api/v1/properties/pine-estate/cabins');
        $list->assertOk();
        $slugs = collect($list->json('data'))->pluck('slug')->all();
        $this->assertContains('lodge-a', $slugs);
        $this->assertNotContains('lodge-b', $slugs);

        $this->getJson('/api/v1/properties/pine-estate/cabins/lodge-a')->assertOk()
            ->assertJsonPath('data.slug', 'lodge-a');
    }

    public function test_cabin_of_other_property_404(): void
    {
        $a = Property::factory()->create(['slug' => 'estate-a']);
        $b = Property::factory()->create(['slug' => 'estate-b']);
        Cabin::factory()->create(['property_id' => $b->id, 'slug' => 'lodge-x']);

        $this->getJson("/api/v1/properties/{$a->slug}/cabins/lodge-x")->assertNotFound();
    }

    public function test_flat_cabin_slug_route_does_not_exist(): void
    {
        $prop = Property::factory()->create();
        $cabin = Cabin::factory()->create(['property_id' => $prop->id]);

        $this->getJson("/api/v1/cabins/{$cabin->slug}")->assertNotFound();
    }
}
