<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\User;

use App\Models\User\ArticleNote;
use Tests\TestCase;

/**
 * Class ArticleNoteTest
 * @package Tests\Athenia\Unit\Models\User
 */
final class ArticleNoteTest extends TestCase
{
    public function testUser(): void
    {
        $articleNote = new ArticleNote();
        $relation = $articleNote->user();

        $this->assertEquals('article_notes.user_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testArticle(): void
    {
        $articleNote = new ArticleNote();
        $relation = $articleNote->article();

        $this->assertEquals('article_notes.article_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('articles.id', $relation->getQualifiedOwnerKeyName());
    }
}
