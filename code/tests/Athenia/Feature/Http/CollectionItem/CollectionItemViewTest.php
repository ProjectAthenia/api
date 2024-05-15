<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\V1\CollectionItem;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class categoriesViewTest
 * @package Tests\Athenia\Feature\V4\categories
 */
final class CollectionItemViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testGetBlocksNotLoggedIn(): void
    {
        CollectionItem::factory()->create([
            'id'    =>  1,
        ]);
        $response = $this->json('GET', '/v1/collection-items/1');
        $response->assertStatus(403);
    }

    public function testGetSingleNotFoundFails(): void
    {
        $this->actAsUser();
        $response = $this->json('GET', '/v1/collection-items/1');
        $response->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $this->actAsUser();
        $response = $this->json('GET', '/v1/collection-items/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleNonPublicBlocked(): void
    {
        $this->actAsUser();
        CollectionItem::factory()->create([
            'id'    =>  1,
            'collection_id' => Collection::factory()->create([
                'is_public' => false,
            ])->id,
        ]);

        $response = $this->json('GET', '/v1/collection-items/1');
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAsUser();
        $model = CollectionItem::factory()->create([
            'id'    =>  1,
            'collection_id' => Collection::factory()->create([
                'is_public' => true,
            ])->id,
        ]);

        $response = $this->json('GET', '/v1/collection-items/1');
        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNonPublicSuccess(): void
    {
        $this->actAsUser();
        $model = CollectionItem::factory()->create([
            'id'    =>  1,
            'collection_id' => Collection::factory()->create([
                'is_public' => false,
                'owner_id' => $this->actingAs->id,
            ])->id,
        ]);

        $response = $this->json('GET', '/v1/collection-items/1');
        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }
}
