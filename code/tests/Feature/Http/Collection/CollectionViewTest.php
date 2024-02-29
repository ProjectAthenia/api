<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Collection;

use App\Models\Collection\Collection;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class categoriesViewTest
 * @package Tests\Feature\V4\categories
 */
final class CollectionViewTest extends TestCase
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
        Collection::factory()->create([
            'id'    =>  1,
            'is_public' => false,
        ]);
        $response = $this->json('GET', '/v1/collections/1');
        $response->assertStatus(403);
    }

    public function testGetSingleNotFoundFails(): void
    {
        $this->actAsUser();
        $response = $this->json('GET', '/v1/collections/1');
        $response->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $this->actAsUser();
        $response = $this->json('GET', '/v1/collections/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleNonPublicBlocked(): void
    {
        $this->actAsUser();
        Collection::factory()->create([
            'id'    =>  1,
            'is_public' => false,
        ]);

        $response = $this->json('GET', '/v1/collections/1');
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAsUser();
        $model = Collection::factory()->create([
            'id'    =>  1,
            'is_public' => true,
        ]);

        $response = $this->json('GET', '/v1/collections/1');
        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNonPublicSuccess(): void
    {
        $this->actAsUser();
        $model = Collection::factory()->create([
            'id'    =>  1,
            'owner_id' => $this->actingAs->id,
            'is_public' => false,
        ]);

        $response = $this->json('GET', '/v1/collections/1');
        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }
}
