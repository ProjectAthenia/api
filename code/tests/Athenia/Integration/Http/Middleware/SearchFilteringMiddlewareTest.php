<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Middleware;

use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SearchFilteringMiddlewareTest
 * @package Tests\Athenia\Integration\Middleware
 */
final class SearchFilteringMiddlewareTest extends TestCase
{
    use DatabaseSetupTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->actAs(Role::ARTICLE_VIEWER);
    }

    public function testSearchWithLike(): void
    {
        Article::factory()->count( 1)->create(['title' => 'h']);
        Article::factory()->count( 1)->create(['title' => 'cart']);
        Article::factory()->count( 1)->create(['title' => 'can']);
        Article::factory()->count( 1)->create(['title' => 'the']);
        Article::factory()->count( 1)->create(['title' => 'Hey']);

        // first page
        $response = $this->json('GET', '/v1/articles?search[title]=like,*h*');
        $response->assertJson([
                'total' => 3,
                'current_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 3,
                'last_page' => 1
            ]);

        $response->assertStatus(200);
    }

    public function testFilter(): void
    {
        Article::factory()->count( 1)->create(['title' => 'h']);
        Article::factory()->count( 1)->create(['title' => 'cart']);
        Article::factory()->count( 1)->create(['title' => 'can']);
        Article::factory()->count( 1)->create(['title' => 'the']);
        Article::factory()->count( 1)->create(['title' => 'butts']);

        // first page
        $response = $this->json('GET', '/v1/articles?filter[title]=butts');
        $response->assertJson([
                'total' => 1,
                'current_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 1,
                'last_page' => 1
            ]);

        $response->assertStatus(200);
    }
}
