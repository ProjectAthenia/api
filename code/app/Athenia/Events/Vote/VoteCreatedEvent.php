<?php
declare(strict_types=1);

namespace App\Athenia\Events\Vote;

use App\Models\Vote\Vote;

/**
 * Class VoteCreatedEvent
 * @package App\Events\Vote
 */
class VoteCreatedEvent
{
    /**
     * @var Vote
     */
    private $vote;

    /**
     * VoteCreatedEvent constructor.
     * @param Vote $vote
     */
    public function __construct(Vote $vote)
    {
        $this->vote = $vote;
    }

    /**
     * @return Vote
     */
    public function getVote(): Vote
    {
        return $this->vote;
    }
}