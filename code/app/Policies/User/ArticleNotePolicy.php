<?php
declare(strict_types=1);

namespace App\Policies\User;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\ArticleNote;
use App\Models\User\User;

/**
 * Class ArticleNotePolicy
 * @package App\Policies\User
 */
class ArticleNotePolicy extends BasePolicyAbstract
{
    /**
     * Any logged in user can view their own article notes
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function all(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * Any logged in user can create article notes for themselves
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * Any logged in user can view their own article note
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param ArticleNote $articleNote
     * @return bool
     */
    public function view(User $loggedInUser, User $requestedUser, ArticleNote $articleNote)
    {
        return $loggedInUser->id == $requestedUser->id &&
            $requestedUser->id == $articleNote->user_id;
    }

    /**
     * Any logged in user can update their own article note
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param ArticleNote $articleNote
     * @return bool
     */
    public function update(User $loggedInUser, User $requestedUser, ArticleNote $articleNote)
    {
        return $loggedInUser->id == $requestedUser->id &&
            $requestedUser->id == $articleNote->user_id;
    }

    /**
     * Any logged in user can delete their own article note
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param ArticleNote $articleNote
     * @return bool
     */
    public function delete(User $loggedInUser, User $requestedUser, ArticleNote $articleNote)
    {
        return $loggedInUser->id == $requestedUser->id &&
            $requestedUser->id == $articleNote->user_id;
    }
}
