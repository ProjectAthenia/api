<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Vote;

use App\Events\Vote\VoteCreatedEvent;
use App\Models\Vote\Vote;
use Tests\TestCase;

/**
 * Class VoteCreatedEventTest
 * @package Tests\Unit\Events\Vote
 */
class VoteCreatedEventTest extends TestCase
{
    public function testGetVote(): void
    {
        $model = new Vote();

        $event = new VoteCreatedEvent($model);

        $this->assertEquals($model, $event->getVote());
    }
}