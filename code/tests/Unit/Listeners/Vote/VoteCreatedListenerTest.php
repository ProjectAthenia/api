<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Vote;

use App\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Events\Vote\VoteCreatedEvent;
use App\Listeners\Vote\VoteCreatedListener;
use App\Models\Vote\BallotItem;
use App\Models\Vote\Vote;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class VoteCreatedListenerTest
 * @package Tests\Unit\Listeners\Vote
 */
class VoteCreatedListenerTest extends TestCase
{
    public function testHandle()
    {
        $vote = new Vote([
            'ballotSubject' => new BallotItem([
                'vote_count' => 34,
                'votes_cast' => 45,
            ]),
            'result' => 2,
        ]);

        $event = new VoteCreatedEvent($vote);

        /** @var BallotItemRepositoryContract|CustomMockInterface $repository */
        $repository = mock(BallotItemRepositoryContract::class);

        $repository->shouldReceive('update')->once()->with($vote->ballotSubject, [
            'vote_count' => 36,
            'votes_cast' => 46,
        ]);

        $listener = new VoteCreatedListener($repository);

        $listener->handle($event);
    }
}
