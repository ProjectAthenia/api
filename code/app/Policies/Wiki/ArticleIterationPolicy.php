<?php
declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Role;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class IterationPolicy
 * @package App\Policies\Wiki
 */
class ArticleIterationPolicy extends BasePolicyAbstract
{
    /**
     * All logged in users can currently see all article iterations right now
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return $user->hasRole([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]);
    }
}
