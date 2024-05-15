<?php
declare(strict_types=1);

namespace App\Policies\Vote;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;
use App\Models\Vote\Ballot;

/**
 * Class BallotCompletionPolicy
 * @package App\Policies\Vote
 */
class BallotCompletionPolicy extends BasePolicyAbstract
{
    /**
     * Anyone can index their ballot completions
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
     * Anyone can see all article versions
     *
     * @param User $user
     * @param Ballot $ballot
     * @return bool
     */
    public function create(User $user, Ballot $ballot)
    {
        return true;
    }
}
