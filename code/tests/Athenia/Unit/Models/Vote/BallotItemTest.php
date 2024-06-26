<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Vote;

use App\Models\Vote\BallotItem;
use Tests\TestCase;

/**
 * Class BallotCompletionTest
 * @package Tests\Athenia\Unit\Models\Vote
 */
final class BallotItemTest extends TestCase
{
    public function testBallot(): void
    {
        $model = new BallotItem();
        $relation = $model->ballot();

        $this->assertEquals('ballots.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_items.ballot_id', $relation->getQualifiedForeignKeyName());
    }

    public function testBallotItems(): void
    {
        $model = new BallotItem();
        $relation = $model->ballotItemOptions();

        $this->assertEquals('ballot_items.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('ballot_item_options.ballot_item_id', $relation->getQualifiedForeignKeyName());
    }
}
