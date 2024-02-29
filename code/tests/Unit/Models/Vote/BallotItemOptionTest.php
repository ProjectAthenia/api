<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Vote;

use App\Models\Vote\BallotItemOption;
use Tests\TestCase;

final class BallotItemOptionTest extends TestCase
{
    public function testBallotItem(): void
    {
        $model = new BallotItemOption();
        $relation = $model->ballotItem();

        $this->assertEquals('ballot_items.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_item_options.ballot_item_id', $relation->getQualifiedForeignKeyName());
    }

    public function testSubject(): void
    {
        $model = new BallotItemOption();
        $relation = $model->subject();

        $this->assertEquals('subject_type', $relation->getMorphType());
        $this->assertEquals('ballot_item_options.subject_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVotes(): void
    {
        $model = new BallotItemOption();
        $relation = $model->votes();

        $this->assertEquals('ballot_item_options.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('votes.ballot_item_option_id', $relation->getQualifiedForeignKeyName());
    }
}
