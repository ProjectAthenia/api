<?php
declare(strict_types=1);

namespace Tests\Integration\Models\Wiki;

use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\Iteration;
use Carbon\Carbon;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleTest
 * @package Tests\Integration\Models\Wiki
 */
class ArticleTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testContentReturnsNull()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        $this->assertNull($article->content);
    }

    public function testCurrentVersionReturnsProperVersion()
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

    public function testContentReturnsModelContent()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var Iteration $iteration This should be appended */
        $iteration = Iteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'Hello'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'iteration_id' => $iteration->id,
        ]);

        $this->assertEquals('Hello', $article->content);
    }

    public function testContentReturnsCorrectModel()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** This should be appended */
        $iteration = Iteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now(),
            'content' => 'Hello'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'iteration_id' => $iteration->id,
        ]);

        /** This is an old iteration that should not be appended */
        $iteration = Iteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'old content'
        ]);

        ArticleVersion::factory()->create([
            'article_id' => $article->id,
            'iteration_id' => $iteration->id,
            'created_at' => Carbon::now()->subDay(),
        ]);

        $this->assertEquals('Hello', $article->content);
    }

    public function testLastIterationContentReturnsModelContent()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var Iteration $iteration This should be appended */
        Iteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'Hello'
        ]);

        $this->assertEquals('Hello', $article->last_iteration_content);
    }

    public function testLastIterationContentReturnsCorrectModel()
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        /** This should be appended */
        Iteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now(),
            'content' => 'Hello'
        ]);

        /** This is an old iteration that should not be appended */
        Iteration::factory()->create([
            'article_id' => $article->id,
            'created_at' => Carbon::now()->subDay(),
            'content' => 'old content'
        ]);

        $this->assertEquals('Hello', $article->last_iteration_content);
    }
}
