<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Vote;

use App\Models\Vote\BallotCompletion;
use Tests\TestCase;

/**
 * Class BallotCompletionTest
 * @package Tests\Athenia\Unit\Models\Vote
 */
final class BallotCompletionTest extends TestCase
{
    public function testBallot(): void
    {
        $model = new BallotCompletion();
        $relation = $model->ballot();

        $this->assertEquals('ballots.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_completions.ballot_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser(): void
    {
        $model = new BallotCompletion();
        $relation = $model->user();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_completions.user_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVotes(): void
    {
        $model = new BallotCompletion();
        $relation = $model->votes();

        $this->assertEquals('ballot_completions.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('votes.ballot_completion_id', $relation->getQualifiedForeignKeyName());
    }
}