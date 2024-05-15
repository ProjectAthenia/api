<?php
declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;

/**
 * Class ArticleVersionPolicy
 * @package App\Policies\Wiki
 */
class ArticleVersionPolicy extends BasePolicyAbstract
{
    /**
     * Anyone can see all article versions
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return $user->hasRole([Role::ARTICLE_VIEWER, Role::ARTICLE_EDITOR]);
    }

    /**
     * Anyone can see all article versions
     *
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function create(User $user, Article $article)
    {
        return $user->id == $article->created_by_id;
    }
}