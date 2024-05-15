<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Article\ArticleVersion;

use App\Models\Role;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\ArticleIteration;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleVersionCreateTest
 * @package Tests\Athenia\Feature\Http\Article\ArticleVersion
 */
final class ArticleVersionCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

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

    public function testNotLoggedInUserBlocked(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('POST', $this->path . $article->id . '/versions');

        $response->assertStatus(403);
    }

    public function testNonOwningUserBlocked(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);
        $article = Article::factory()->create();
        $response = $this->json('POST', $this->path . $article->id . '/versions');

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);
        $iteration = ArticleIteration::factory()->create([
            'article_id' => $article->id,
        ]);

        $response = $this->json('POST', $this->path . $article->id . '/versions', [
            'article_iteration_id' => $iteration->id,
        ]);

        $response->assertStatus(201);

        $articleVersion = ArticleVersion::first();
        $this->assertEquals($articleVersion->article_iteration_id, $iteration->id);
    }

    public function testCreateInvalidIntegerFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path . $article->id . '/versions', [
            'article_iteration_id' => 'hi',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'article_iteration_id' => ['The article iteration id must be an integer.'],
            ],
        ]);
    }

    public function testCreateInvalidModelFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path . $article->id . '/versions', [
            'article_iteration_id' => 245,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'article_iteration_id' => ['The selected article iteration id is invalid.'],
            ],
        ]);
    }

    public function testCreateFailsIterationNotFromArticle(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);
        $iteration = ArticleIteration::factory()->create();

        $response = $this->json('POST', $this->path . $article->id . '/versions', [
            'article_iteration_id' => $iteration->id,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'article_iteration_id' => ['The selected article iteration id does not seem to be from the related article.'],
            ],
        ]);
    }
}
