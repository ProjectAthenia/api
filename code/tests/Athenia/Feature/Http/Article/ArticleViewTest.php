<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Article;

use App\Models\Role;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\ArticleIteration;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleViewTest
 * @package Tests\Athenia\Feature\Http\Article
 */
final class ArticleViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/articles/';

    /**
     * @var Article
     */
    private $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->article = Article::factory()->create();
        $this->path.= $this->article->id;
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('GET', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]) as $role) {
            $this->actAs($role);

            $response = $this->json('GET', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET',   '/v1/articles/1435');

        $response->assertStatus(404);
    }

    public function testViewSuccessful(): void
    {
        $this->actAs(Role::ARTICLE_VIEWER);

        $iteration = ArticleIteration::factory()->create([
            'content' => 'hello',
            'article_id' => $this->article->id,
        ]);
        ArticleVersion::factory()->create([
            'article_id' => $this->article->id,
            'article_iteration_id' => $iteration->id,
        ]);

        $response = $this->json('GET', $this->path);

        $response->assertStatus(200);

        $data = $this->article->toArray();
        unset($data['resource']);

        $response->assertJson($data);

        $this->assertNotNull($response->json()['content']);
        $this->assertEquals($this->article->content, $response->json()['content']);
    }
}
