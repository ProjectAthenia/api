<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Article;

use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleIndexTest
 * @package Tests\Athenia\Feature\Http\Article
 */
final class ArticleIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/articles';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedUserBlocked(): void
    {
        $response = $this->json('GET', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_VIEWER, Role::ARTICLE_EDITOR]) as $role ) {
            $this->actAs($role);
            $response = $this->json('GET', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testGetPaginationEmpty(): void
    {
        foreach ([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER] as $role) {
            $this->actAs($role);

            $response = $this->json('GET', $this->path);

            $response->assertStatus(200);
            $response->assertJson([
                'total' => 0,
                'data' => []
            ]);
        }
    }

    public function testGetPaginationResult(): void
    {
        $this->actAs(Role::ARTICLE_VIEWER);
        Article::factory()->count(15)->create();

        // first page
        $response = $this->json('GET', $this->path);
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
                    '*' =>  array_keys((new Article())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . '?page=2');
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
                    '*' =>  array_keys((new Article())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . '?page=2&limit=5');
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
                    '*' =>  array_keys((new Article())->toArray())
                ]
            ]);
    }
}
