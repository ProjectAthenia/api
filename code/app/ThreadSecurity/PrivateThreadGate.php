<?php
declare(strict_types=1);

namespace App\ThreadSecurity;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Models\User\Thread;
use App\Models\User\User;

/**
 * Class PrivateThreadGate
 * @package App\ThreadSecurity
 */
class PrivateThreadGate implements ThreadSubjectGateContract
{
    /**
     * Authorizes the passed in user to make sure that they can access the thread subject
     * The optional id passed in if we are authorizing a specific subject id
     *
     * @param User $user
     * @param null $id
     * @return bool
     */
    public function authorizeSubject(User $user, $id = null): bool
    {
        return true;
    }

    /**
     * Authorizes that a user can post to a specific thread
     *
     * @param User $user
     * @param Thread $thread
     * @return bool
     */
    public function authorizeThread(User $user, Thread $thread): bool
    {
        return $thread->users->pluck('id')->contains($user->id);
    }
}