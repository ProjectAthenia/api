<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\User\UserMergeEvent;
use App\Listeners\User\UserMerge\UserMessagesMergeListener;
use App\Models\User\Message;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserMessagesMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
class UserMessagesMergeListenerTest extends TestCase
{
    /**
     * @var MessageRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserMessagesMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(MessageRepositoryContract::class);
        $this->listener = new UserMessagesMergeListener($this->repository);
    }

    public function testHandleWithoutMerge()
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

    public function testHandleWithMerge()
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
            'messages' => new Collection([
                new Message()
            ])
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'messages' => true,
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser->messages->first(), [
            'user_id' => $mainUser->id,
        ]);

        $this->listener->handle($event);
    }
}