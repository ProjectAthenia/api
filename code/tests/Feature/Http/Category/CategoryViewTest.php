<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Category;

use App\Models\Category;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class categoriesViewTest
 * @package Tests\Feature\V4\categories
 */
class CategoryViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testGetSingleSuccess()
    {
        $model = Category::factory()->create([
            'id'    =>  1
        ]);

        $response = $this->json('GET', '/v1/categories/1');
        $response->assertJson($model->toArray());
        $response->assertStatus(200);
    }

    public function testGetSingleNotFoundFails()
    {
        $response = $this->json('GET', '/v1/categories/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $response = $this->json('GET', '/v1/categories/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleAssetsEmpty()
    {
        $model = Category::factory()->create([
            'id'    =>  1
        ]);

        $response = $this->json('GET', '/v1/categories/1');
        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }
}
