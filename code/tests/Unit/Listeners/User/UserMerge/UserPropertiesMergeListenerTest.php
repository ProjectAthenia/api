<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Events\User\UserMergeEvent;
use App\Listeners\User\UserMerge\UserPropertiesMergeListener;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserPropertiesMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
class UserPropertiesMergeListenerTest extends TestCase
{
    /**
     * @var UserRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserPropertiesMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = mock(UserRepositoryContract::class);
        $this->listener = new UserPropertiesMergeListener($this->repository);
    }

    public function testHandleWithoutOptions(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
        ]);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->repository->shouldReceive('update')->once()->with($mergeUser, [
            'merged_to_id' => $mainUser->id,
            'deleted_at' => $now,
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser);

        $this->listener->handle($event);
    }

    public function testHandleWithOptions(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
            'subscriptions' => new Collection([
                new Subscription(),
            ]),
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
        ]);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->repository->shouldReceive('update')->once()->with($mainUser, [
            'email' => 'testy@test.com',
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser, [
            'merged_to_id' => $mainUser->id,
            'deleted_at' => $now,
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'email' => true,
            'subscriptions' => true,
        ]);

        $this->listener->handle($event);
    }
}