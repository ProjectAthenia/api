<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;
use App\Athenia\Listeners\User\UserMerge\UserMessagesMergeListener;
use App\Models\Messaging\Message;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserMessagesMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
final class UserMessagesMergeListenerTest extends TestCase
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