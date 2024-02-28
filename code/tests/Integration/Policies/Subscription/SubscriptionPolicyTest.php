<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Subscription;

use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Policies\Subscription\SubscriptionPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SubscriptionPolicyTest
 * @package Tests\Integration\Policies\Subscription
 */
class SubscriptionPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAll(): void
    {
        $policy = new SubscriptionPolicy();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse($policy->all($user1, $user2));
        $this->assertTrue($policy->all($user1, $user1));
    }

    public function testCreate(): void
    {
        $policy = new SubscriptionPolicy();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse($policy->create($user1, $user2));
        $this->assertTrue($policy->create($user1, $user1));
    }

    public function testUpdate(): void
    {
        $policy = new SubscriptionPolicy();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $user1->id,
        ]);

        $this->assertFalse($policy->update($user1, $user2, $subscription));
        $this->assertFalse($policy->update($user2, $user2, $subscription));
        $this->assertTrue($policy->update($user1, $user1, $subscription));
    }
}
