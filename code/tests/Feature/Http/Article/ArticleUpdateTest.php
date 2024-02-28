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
 * Class ArticleUpdateTest
 * @package Tests\Feature\Http\Article
 */
class ArticleUpdateTest extends TestCase
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

    public function testNotLoggedInUserBlocked(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('PUT', $this->path . '/' . $article->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $this->actAs($role);

            $article = Article::factory()->create([
                'created_by_id' => $this->actingAs->id,
            ]);
            $response = $this->json('PUT', $this->path . '/' . $article->id);

            $response->assertStatus(403);
        }
    }

    public function testNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/1');

        $response->assertStatus(404);
    }

    public function testUpdateSuccessful(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'title' => 'A title',
            'created_by_id' => $this->actingAs->id,
        ]);

        $data = [
            'title' => 'A different title',
        ];

        $response = $this->json('PUT', $this->path . '/' . $article->id, $data);

        $response->assertStatus(200);
        $response->assertJson($data);

        /** @var Article $updated */
        $updated = Article::find($article->id);

        $this->assertEquals('A different title', $updated->title);
    }

    public function testUpdateBlockedUserHasNotCreatedArticle(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);
        
        $article = Article::factory()->create();
        $response = $this->json('PUT', $this->path . '/' . $article->id);

        $response->assertStatus(403);
    }

    public function testUpdateFailsInvalidStringFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'title' => 1,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsStringsTooLong(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'title' => str_repeat('a', 121),
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title may not be greater than 120 characters.'],
            ]
        ]);
    }
}
