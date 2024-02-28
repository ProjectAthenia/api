<?php
declare(strict_types=1);

namespace Tests\Integration\Console\Commands;

use App\Console\Commands\SendRenewalReminders;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Repositories\Subscription\MembershipPlanRateRepository;
use App\Repositories\Subscription\SubscriptionRepository;
use Carbon\Carbon;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SendRenewalRemindersTest
 * @package Tests\Integration\Console\Commands
 */
class SendRenewalRemindersTest extends TestCase
{
    use DatabaseSetupTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testHandle(): void
    {
        $subscriptionRepository = new SubscriptionRepository(
            new Subscription(),
            $this->getGenericLogMock(),
            new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->getGenericLogMock(),
            )
        );
        /** @var MessageRepositoryContract|CustomMockInterface $messageRepository */
        $messageRepository = mock(MessageRepositoryContract::class);

        $command = new SendRenewalReminders($subscriptionRepository, $messageRepository);

        /** @var Subscription $subscription */
        $subscription = Subscription::factory()->create([
            'expires_at' => Carbon::now()->addWeek(2),
            'membership_plan_rate_id' => MembershipPlanRate::factory()->create()->id,
        ]);
        Subscription::factory()->create([
            'expires_at' => Carbon::now()->addWeek()->addDay(6),
            'membership_plan_rate_id' => MembershipPlanRate::factory()->create()->id,
        ]);
        Subscription::factory()->create([
            'expires_at' => Carbon::now()->addWeek(2)->addDay(1),
            'membership_plan_rate_id' => MembershipPlanRate::factory()->create()->id,
        ]);

        $messageRepository->shouldReceive('sendEmailToUser')->once()->with(
            \Mockery::on(function($user) use($subscription) {
                $this->assertEquals($user, $subscription->subscriber);
                return true;
            }),
            'Membership Renewal Reminder',
            'renewal-reminder',
            \Mockery::on(function($data) use($subscription) {

                $this->assertArrayHasKey('membership_name', $data);
                $this->assertArrayHasKey('recurring', $data);
                $this->assertArrayHasKey('membership_cost', $data);

                return true;
            })
        );

        $command->handle();
    }
}
