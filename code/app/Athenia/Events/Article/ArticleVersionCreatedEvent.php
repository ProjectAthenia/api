<?php
declare(strict_types=1);

namespace App\Athenia\Events\Article;

use App\Models\Wiki\ArticleVersion;

/**
 * Class ArticleVersionCreatedEvent
 * @package App\Events\Article
 */
class ArticleVersionCreatedEvent
{
    /**
     * @var ArticleVersion
     */
    private $newVersion;

    /**
     * @var ArticleVersion
     */
    private $oldVersion;

    /**
     * ArticleVersionCreatedEvent constructor.
     * @param ArticleVersion $newVersion
     * @param ArticleVersion|null $oldVersion
     */
    public function __construct(ArticleVersion $newVersion, ?ArticleVersion $oldVersion)
    {
        $this->newVersion = $newVersion;
        $this->oldVersion = $oldVersion;
    }

    /**
     * @return ArticleVersion
     */
    public function getNewVersion(): ArticleVersion
    {
        return $this->newVersion;
    }

    /**
     * @return ArticleVersion|null
     */
    public function getOldVersion(): ?ArticleVersion
    {
        return $this->oldVersion;
    }
}