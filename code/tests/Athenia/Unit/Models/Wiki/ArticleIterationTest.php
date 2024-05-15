<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Wiki;

use App\Models\Wiki\ArticleIteration;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class IterationTest
 * @package Tests\Athenia\Unit\Models\Wiki
 */
final class ArticleIterationTest extends TestCase
{
    public function testArticle(): void
    {
        $article = new ArticleIteration();
        $relation = $article->article();

        $this->assertEquals('articles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_iterations.article_id', $relation->getQualifiedForeignKeyName());
    }

    public function testCreatedBy(): void
    {
        $article = new ArticleIteration();
        $relation = $article->createdBy();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_iterations.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testModification(): void
    {
        $article = new ArticleIteration();
        $relation = $article->modification();

        $this->assertEquals('article_modifications.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_iterations.article_modification_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVersions(): void
    {
        $article = new ArticleIteration();
        $relation = $article->version();

        $this->assertEquals('article_iterations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('article_versions.article_iteration_id', $relation->getQualifiedForeignKeyName());
    }
}
