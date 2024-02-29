<?php
declare(strict_types=1);

namespace Tests\Integration\Models\Wiki;

use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\ArticleIteration;
use Carbon\Carbon;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleTest
 * @package Tests\Integration\Models\Wiki
 */
final class ArticleTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testContentReturnsNull(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        $this->assertNull($article->content);
    }

    public function testCurrentVersionReturnsProperVersion(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
        ]);

        $expected = ArticleVersion::factory()->create([
            'article_id' => $article->id,
        ]);

        $this->assertEquals($expected->id, $article->current_version->id);
    }

    public function testContentReturnsModelContent(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var ArticleIteration $iteration This should be appended */
        $iteration = ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'Hello'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'article_iteration_id' => $iteration->id,
        ]);

        $this->assertEquals('Hello', $article->content);
    }

    public function testContentReturnsCorrectModel(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** This should be appended */
        $iteration = ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now(),
            'content' => 'Hello'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'article_iteration_id' => $iteration->id,
        ]);

        /** This is an old iteration that should not be appended */
        $iteration = ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'old content'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'article_iteration_id' => $iteration->id,
            'created_at' => Carbon::now()->subDay(),
        ]);

        $this->assertEquals('Hello', $article->content);
    }

    public function testLastIterationContentReturnsModelContent(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var ArticleIteration $iteration This should be appended */
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'Hello'
        ]);

        $this->assertEquals('Hello', $article->last_iteration_content);
    }

    public function testLastIterationContentReturnsCorrectModel(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** This should be appended */
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now(),
            'content' => 'Hello'
        ]);

        /** This is an old iteration that should not be appended */
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now()->subDay(),
            'content' => 'old content'
        ]);

        $this->assertEquals('Hello', $article->last_iteration_content);
    }
}
