<?php
declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;

/**
 * Class ArticlePolicy
 * @package App\Policies\Wiki
 */
class ArticlePolicy extends BasePolicyAbstract
{
    /**
     * All logged in users can currently see all articles right now
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return $user->hasRole([
            Role::ARTICLE_VIEWER,
            Role::ARTICLE_EDITOR,
        ]);
    }

    /**
     * Any logged in users can view a single article
     *
     * @param User $user
     * @param Article $model
     * @return bool
     */
    public function view(User $user, Article $model)
    {
        return $user->hasRole([
            Role::ARTICLE_VIEWER,
            Role::ARTICLE_EDITOR,
        ]);
    }

    /**
     * Any logged in users can create an article
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole(Role::ARTICLE_EDITOR);
    }

    /**
     * Right now only users that created the article can update it
     *
     * @param User $user
     * @param Article $model
     * @return bool
     */
    public function update(User $user, Article $model)
    {
        return $user->hasRole(Role::ARTICLE_EDITOR) && $user->id == $model->created_by_id;
    }
}