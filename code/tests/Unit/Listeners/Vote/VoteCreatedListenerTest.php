<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Vote;

use App\Contracts\Repositories\Vote\BallotItemOptionRepositoryContract;
use App\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Events\Vote\VoteCreatedEvent;
use App\Listeners\Vote\VoteCreatedListener;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Vote\Vote;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class VoteCreatedListenerTest
 * @package Tests\Unit\Listeners\Vote
 */
final class VoteCreatedListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $vote = new Vote([
            'ballotItemOption' => new BallotItemOption([
                'vote_count' => 34,
                'ballotItem' => new BallotItem([
                    'votes_cast' => 45,
                ]),
            ]),
            'result' => 2,
        ]);

        $event = new VoteCreatedEvent($vote);

        /** @var BallotItemRepositoryContract|CustomMockInterface $ballotItemRepository */
        $ballotItemRepository = mock(BallotItemRepositoryContract::class);

        /** @var BallotItemOptionRepositoryContract|CustomMockInterface $ballotItemOptionRepository */
        $ballotItemOptionRepository = mock(BallotItemOptionRepositoryContract::class);

        $ballotItemRepository->shouldReceive('update')->once()->with($vote->ballotItemOption->ballotItem, [
            'votes_cast' => 46,
        ]);
        $ballotItemOptionRepository->shouldReceive('update')->once()->with($vote->ballotItemOption, [
            'vote_count' => 36,
        ]);

        $listener = new VoteCreatedListener($ballotItemRepository, $ballotItemOptionRepository);

        $listener->handle($event);
    }
}
