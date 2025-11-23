<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Wiki\ArticleSummary;

use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleSummary;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleSummaryUpdateTest
 * @package Tests\Athenia\Feature\Wiki\ArticleSummary
 */
final class ArticleSummaryUpdateTest extends TestCase
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
        ArticleSummary::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->json('PUT', $this->path);

        $response->assertStatus(403);
    }

    public function testUserWithoutRoleBlocked(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ArticleSummary::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->json('PUT', $this->path, [
            'content' => 'Updated summary content',
        ]);

        $response->assertStatus(403);
    }

    public function testNotFound(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        $response = $this->json('PUT', $this->path, [
            'content' => 'Updated content',
        ]);

        $response->assertStatus(404);
    }

    public function testUpdateSuccessful(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        ArticleSummary::factory()->create([
            'article_id' => $this->article->id,
            'content' => 'Original summary content',
        ]);

        $response = $this->json('PUT', $this->path, [
            'content' => 'Updated summary content',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'article_id' => $this->article->id,
            'content' => 'Updated summary content',
        ]);
    }

    public function testUpdateFailsInvalidStringFields(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $this->actingAs($user);

        ArticleSummary::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->json('PUT', $this->path, [
            'content' => 12345,
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['content']);
    }
}
