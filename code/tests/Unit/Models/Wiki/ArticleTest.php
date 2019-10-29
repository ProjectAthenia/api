<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Article;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class ArticleTest
 * @package Tests\Unit\Models\Wiki
 */
class ArticleTest extends TestCase
{
    public function testCreatedBy()
    {
        $article = new Article();
        $relation = $article->createdBy();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('articles.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testIterations()
    {
        $article = new Article();
        $relation = $article->iterations();

        $this->assertEquals('articles.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('iterations.article_id', $relation->getQualifiedForeignKeyName());

        $this->assertStringContainsString('order by', $relation->toSql());
        $this->assertStringContainsString('created_at', $relation->toSql());
        $this->assertStringContainsString('desc', $relation->toSql());
    }

    public function testVersions()
    {
        $article = new Article();
        $relation = $article->versions();

        $this->assertEquals('articles.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('article_versions.article_id', $relation->getQualifiedForeignKeyName());

        $this->assertStringContainsString('order by', $relation->toSql());
        $this->assertStringContainsString('created_at', $relation->toSql());
        $this->assertStringContainsString('desc', $relation->toSql());
    }
}