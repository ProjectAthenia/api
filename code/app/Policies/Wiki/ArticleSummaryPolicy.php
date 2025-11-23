<?php
declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;

/**
 * Class ArticleSummaryPolicy
 * @package App\Policies\Wiki
 */
class ArticleSummaryPolicy extends BasePolicyAbstract
{
    /**
     * Any logged in users can view an article summary
     *
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function view(User $user, Article $article)
    {
        return true;
    }

    /**
     * Only users with the ARTICLE_EDITOR role can create article summaries
     *
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function create(User $user, Article $article)
    {
        return $user->hasRole(Role::ARTICLE_EDITOR);
    }

    /**
     * Only users with the ARTICLE_EDITOR role can update article summaries
     *
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function update(User $user, Article $article)
    {
        return $user->hasRole(Role::ARTICLE_EDITOR);
    }

    /**
     * Only users with the ARTICLE_EDITOR role can delete article summaries
     *
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function delete(User $user, Article $article)
    {
        return $user->hasRole(Role::ARTICLE_EDITOR);
    }
}
