<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\User;
use App\Models\Wiki\Article;

/**
 * Interface ArticleRepositoryContract
 * @package App\Contracts\Repositories\Wiki
 */
interface ArticleRepositoryContract extends BaseRepositoryContract
{
    /**
     * Selects an article for a user based on their note completion status and article statistics
     *
     * @param User $user
     * @return Article|null
     */
    public function selectArticleForUser(User $user): ?Article;
}