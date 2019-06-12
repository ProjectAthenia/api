<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Events\User\UserMergeEvent;
use App\Listeners\User\UserMerge\UserSubscriptionsMergeListener;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserSubscriptionsMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
class UserSubscriptionsMergeListenerTest extends TestCase
{
    /**
     * @var SubscriptionRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserSubscriptionsMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(SubscriptionRepositoryContract::class);
        $this->listener = new UserSubscriptionsMergeListener($this->repository);
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
            'subscriptions' => new Collection([
                new Subscription()
            ])
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'subscriptions' => true,
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser->subscriptions->first(), [
            'owner_id' => $mainUser->id,
            'payment_method_id' => null,
        ]);

        $this->listener->handle($event);
    }
}