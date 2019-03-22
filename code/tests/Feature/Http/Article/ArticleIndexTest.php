<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Article;

use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleIndexTest
 * @package Tests\Feature\Http\Article
 */
class ArticleIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/articles';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedUserBlocked()
    {
        $response = $this->json('GET', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked()
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_VIEWER, Role::ARTICLE_EDITOR]) as $role ) {
            $this->actAs($role);
            $response = $this->json('GET', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testGetPaginationEmpty()
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

    public function testGetPaginationResult()
    {
        $this->actAs(Role::ARTICLE_VIEWER);
        factory(Article::class, 15)->create();

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