<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\ArticleVersion;
use Tests\TestCase;

/**
 * Class ArticleVersionTest
 * @package Tests\Unit\Models\Wiki
 */
class ArticleVersionTest extends TestCase
{
    public function testArticle()
    {
        $article = new ArticleVersion();
        $relation = $article->article();

        $this->assertEquals('articles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_versions.article_id', $relation->getQualifiedForeignKeyName());
    }

    public function testIteration()
    {
        $article = new ArticleVersion();
        $relation = $article->iteration();

        $this->assertEquals('article_iterations.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_versions.article_iteration_id', $relation->getQualifiedForeignKeyName());
    }
}
