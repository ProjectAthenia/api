<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Article\Iteration;

use App\Models\Role;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleIterationIndexTest
 * @package Tests\Feature\Http\Article\Iteration
 */
class ArticleIterationIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/articles/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotFound()
    {
        $response = $this->json('GET', $this->path . '124/iterations');

        $response->assertStatus(404);
    }

    public function testNotLoggedUserBlocked()
    {
        $article = Article::factory()->create();
        $response = $this->json('GET', $this->path . $article->id . '/iterations');

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked()
    {
        $article = Article::factory()->create();
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_VIEWER, Role::ARTICLE_EDITOR]) as $role) {
            $this->actAs($role);

            $response = $this->json('GET', $this->path . $article->id . '/iterations');

            $response->assertStatus(403);
        }
    }

    public function testGetPaginationEmpty()
    {
        $article = Article::factory()->create();
        foreach ([Role::ARTICLE_VIEWER, Role::ARTICLE_EDITOR] as $role) {
            $this->actAs($role);
            $response = $this->json('GET', $this->path . $article->id . '/iterations');

            $response->assertStatus(200);
            $response->assertJson([
                'total' => 0,
                'data' => []
            ]);
        }
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::ARTICLE_VIEWER);
        $article = Article::factory()->create();
        ArticleIteration::factory()->count(15)->create([
            'article_id' => $article->id,
        ]);

        ArticleIteration::factory()->count(7)->create();

        // first page
        $response = $this->json('GET', $this->path . $article->id . '/iterations');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleIteration())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $article->id . '/iterations?page=2');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 10,
            'from' => 11,
            'to' => 15,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleIteration())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $article->id . '/iterations?page=2&limit=5');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
            'last_page' => 3
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleIteration())->toArray())
                ]
            ]);
    }
}
