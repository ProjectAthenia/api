<?php
declare(strict_types=1);

namespace App\Athenia\ThreadSecurity;

use App\Athenia\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Models\Messaging\Thread;
use App\Models\User\User;

/**
 * Class GeneralThreadGate
 * @package App\ThreadSecurity
 */
class GeneralThreadGate implements ThreadSubjectGateContract
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
        return true;
    }
}