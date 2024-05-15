<?php
declare(strict_types=1);

namespace App\Policies\Vote;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;
use App\Models\Vote\Ballot;

/**
 * Class BallotPolicy
 * @package App\Policies\Vote
 */
class BallotPolicy extends BasePolicyAbstract
{
    /**
     * Anyone can view a ballot by default
     *
     * @param User $user
     * @param Ballot $ballot
     * @return bool
     */
    public function view(User $user, Ballot $ballot)
    {
        return true;
    }
}
