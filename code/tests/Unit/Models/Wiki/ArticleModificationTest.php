<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\ArticleModification;
use Tests\TestCase;

/**
 * Class ArticleModificationTest
 * @package Tests\Unit\Models\Wiki
 */
class ArticleModificationTest extends TestCase
{
    public function testArticle()
    {
        $article = new ArticleModification();
        $relation = $article->article();

        $this->assertEquals('articles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('article_modifications.article_id', $relation->getQualifiedForeignKeyName());
    }

    public function testIterations()
    {
        $article = new ArticleModification();
        $relation = $article->iteration();

        $this->assertEquals('article_modifications.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('article_iterations.article_modification_id', $relation->getQualifiedForeignKeyName());
    }
}
