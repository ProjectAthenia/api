<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Article;

use App\Events\Article\ArticleVersionCreatedEvent;
use App\Models\Wiki\ArticleVersion;
use Tests\TestCase;

/**
 * Class ArticleVersionCreatedEventTest
 * @package Tests\Unit\Events\Article
 */
class ArticleVersionCreatedEventTest extends TestCase
{
    public function testGetNewVersion()
    {
        $newVersion = new ArticleVersion();
        $newVersion->id = 455;
        $oldVersion = new ArticleVersion();
        $oldVersion->id = 346;

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);

        $this->assertEquals($newVersion, $event->getNewVersion());
    }

    public function testGetOldVersion()
    {
        $newVersion = new ArticleVersion();
        $newVersion->id = 455;
        $oldVersion = new ArticleVersion();
        $oldVersion->id = 346;

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);

        $this->assertEquals($oldVersion, $event->getOldVersion());
    }
}