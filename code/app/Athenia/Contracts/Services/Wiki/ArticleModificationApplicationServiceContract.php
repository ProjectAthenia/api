<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Wiki;

use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleModification;

/**
 * Interface ArticleModificationApplicationServiceContract
 * @package App\Contracts\Services\Wiki
 */
interface ArticleModificationApplicationServiceContract
{
    /**
     * Runs all changes needed for the past through article modification
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function applyModification(User $user, ArticleModification $articleModification) : ?Article;

    /**
     * Handles a remove action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleRemoveAction(User $user, ArticleModification $articleModification) : ?Article;

    /**
     * Handles an add action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleAddAction(User $user, ArticleModification $articleModification) : ?Article;

    /**
     * Handles a replace action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleReplaceAction(User $user, ArticleModification $articleModification) : ?Article;
}
