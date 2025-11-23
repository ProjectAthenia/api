<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Wiki\ArticleSummary;

use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleSummaryCreateTest
 * @package Tests\Athenia\Feature\Wiki\ArticleSummary
 */
final class ArticleSummaryCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    private string $path;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->article = Article::factory()->create();
        $this->path = '/v1/articles/' . $this->article->id . '/article-summary';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testUserWithoutRoleBlocked(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', $this->path, [
            'content' => 'Test summary content',
        ]);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        $response = $this->json('POST', $this->path, [
            'content' => 'This is a test summary for the article.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'article_id' => $this->article->id,
            'content' => 'This is a test summary for the article.',
        ]);
    }

    public function testCreateFailsMissingRequiredFields(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        $response = $this->json('POST', $this->path, []);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['content']);
    }

    public function testCreateFailsInvalidStringFields(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        $response = $this->json('POST', $this->path, [
            'content' => 12345,
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['content']);
    }
}
