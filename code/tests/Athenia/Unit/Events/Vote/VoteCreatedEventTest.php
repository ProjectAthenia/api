<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Vote;

use App\Athenia\Events\Vote\VoteCreatedEvent;
use App\Models\Vote\Vote;
use Tests\TestCase;

/**
 * Class VoteCreatedEventTest
 * @package Tests\Athenia\Unit\Events\Vote
 */
final class VoteCreatedEventTest extends TestCase
{
    public function testGetVote(): void
    {
        $model = new Vote();

        $event = new VoteCreatedEvent($model);

        $this->assertEquals($model, $event->getVote());
    }
}