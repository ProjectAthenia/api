<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;
use App\Athenia\Listeners\User\UserMerge\UserBallotCompletionsMergeListener;
use App\Models\User\User;
use App\Models\Vote\BallotCompletion;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserBallotCompletionsMergeListenerTest
 * @package Tests\Athenia\Unit\Listeners\User\UserMerge
 */
final class UserBallotCompletionsMergeListenerTest extends TestCase
{
    /**
     * @var BallotCompletionRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserBallotCompletionsMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(BallotCompletionRepositoryContract::class);
        $this->listener = new UserBallotCompletionsMergeListener($this->repository);
    }

    public function testHandleWithoutMerge(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser);

        $this->listener->handle($event);
    }

    public function testHandleWithMerge(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
            'ballotCompletions' => new Collection([
                new BallotCompletion()
            ])
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'ballot_completions' => true,
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser->ballotCompletions->first(), [
            'user_id' => $mainUser->id,
        ]);

        $this->listener->handle($event);
    }
}