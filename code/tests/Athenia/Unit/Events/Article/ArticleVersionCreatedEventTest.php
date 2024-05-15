<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Article;

use App\Athenia\Events\Article\ArticleVersionCreatedEvent;
use App\Models\Wiki\ArticleVersion;
use Tests\TestCase;

/**
 * Class ArticleVersionCreatedEventTest
 * @package Tests\Athenia\Unit\Events\Article
 */
final class ArticleVersionCreatedEventTest extends TestCase
{
    public function testGetNewVersion(): void
    {
        $newVersion = new ArticleVersion();
        $newVersion->id = 455;
        $oldVersion = new ArticleVersion();
        $oldVersion->id = 346;

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);

        $this->assertEquals($newVersion, $event->getNewVersion());
    }

    public function testGetOldVersion(): void
    {
        $newVersion = new ArticleVersion();
        $newVersion->id = 455;
        $oldVersion = new ArticleVersion();
        $oldVersion->id = 346;

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);

        $this->assertEquals($oldVersion, $event->getOldVersion());
    }
}