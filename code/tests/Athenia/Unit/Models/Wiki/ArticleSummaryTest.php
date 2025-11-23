<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Wiki;

use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleSummary;
use Tests\TestCase;

/**
 * Class ArticleSummaryTest
 * @package Tests\Athenia\Unit\Models\Wiki
 */
class ArticleSummaryTest extends TestCase
{
    public function testArticle(): void
    {
        $summary = new ArticleSummary([
            'article_id' => 324,
        ]);
        $relation = $summary->article();
        $this->assertEquals('article_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(Article::class, $relation->getRelated());
    }
}
