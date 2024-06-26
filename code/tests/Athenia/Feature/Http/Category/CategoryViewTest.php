<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Category;

use App\Models\Category;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class categoriesViewTest
 * @package Tests\Athenia\Feature\V4\categories
 */
final class CategoryViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testGetSingleSuccess(): void
    {
        $model = Category::factory()->create([
            'id'    =>  1
        ]);

        $response = $this->json('GET', '/v1/categories/1');
        $response->assertJson($model->toArray());
        $response->assertStatus(200);
    }

    public function testGetSingleNotFoundFails(): void
    {
        $response = $this->json('GET', '/v1/categories/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $response = $this->json('GET', '/v1/categories/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
