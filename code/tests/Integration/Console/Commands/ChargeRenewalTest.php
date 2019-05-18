<?php
declare(strict_types=1);

namespace Tests\Integration\Console\Commands;

use App\Console\Commands\ChargeRenewal;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Models\Payment\PaymentMethod;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Repositories\Subscription\MembershipPlanRateRepository;
use App\Repositories\Subscription\SubscriptionRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ChargeRenewalTest
 * @package Tests\Integration\Console\Commands
 */
class ChargeRenewalTest extends TestCase
{
    use DatabaseSetupTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testHandle()
    {
        $now = new Carbon('2018-01-21 12:33:23');
        Carbon::setTestNow($now);

        /** @var Subscription $nonRecurringSubscription */
        $nonRecurringSubscription = factory(Subscription::class)->create([
            'recurring' => false,
            'expires_at' => $now,
        ]);
        /** @var Subscription $stripeSubscription */
        $stripeSubscription = factory(Subscription::class)->create([
            'recurring' => true,
            'expires_at' => $now,
            'membership_plan_rate_id' => factory(MembershipPlanRate::class)->create([
                'cost' => 35,
            ])->id,
            'payment_method_id' => factory(PaymentMethod::class)->create([
                'payment_method_type' => 'stripe',
                'payment_method_key' => 'test_stripe',
            ])->id,
        ]);

        $messageRepository = mock(MessageRepositoryContract::class);
        $paymentService = mock(StripePaymentServiceContract::class);
        $subscriptionRepository = new SubscriptionRepository(
            new Subscription(),
            $this->getGenericLogMock(),
            new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->getGenericLogMock(),
            )
        );
        $config = mock(Repository::class);
        $config->shouldReceive('get')->once()->with('app.name')->andReturn('Athenia');

        $command = new ChargeRenewal($paymentService, $subscriptionRepository, $messageRepository, $config);

        $paymentService->shouldReceive('createPayment')->once()->with(\Mockery::on(function ($user) use ($stripeSubscription) {
            $this->assertEquals($user->id, $stripeSubscription->subscriber_id);
            return true;
        }), 35.0, \Mockery::on(function(PaymentMethod $paymentMethod) {
            return true;
        }), 'Subscription renewal for ' . $stripeSubscription->membershipPlanRate->membershipPlan->name, [
            'subscription_id' => $stripeSubscription->id,
        ]);

        $messageRepository->shouldReceive('sendEmailToUser')->once()->with(
            \Mockery::on(function($user) use($stripeSubscription) {
                return $user->id == $stripeSubscription->subscriber_id;
            }),
            'Athenia Membership Successfully Renewed',
            'membership-renewed',
            \Mockery::on(function($data) use($stripeSubscription) {

                if (!Arr::has($data, ['membership_name', 'membership_cost', 'expiration_date'])) {
                    return false;
                }

                if (!Str::contains($data['membership_cost'], '35.00')) {
                    return false;
                }
                if (!Str::contains($data['membership_name'], $stripeSubscription->membershipPlanRate->membershipPlan->name)) {
                    return false;
                }
                if (!Str::contains($data['expiration_date'], 'January 21st 2019')) {
                    return false;
                }

                return true;
            })
        );

        $messageRepository->shouldReceive('sendEmailToUser')->once()->with(
            \Mockery::on(function($user) use($nonRecurringSubscription) {
                return $user->id == $nonRecurringSubscription->subscriber_id;
            }),
            'Athenia Membership Expired',
            'membership-expired',
            \Mockery::on(function($data) {

                if (!Arr::has($data, ['membership_name'])) {
                    return false;
                }

                return true;
            })
        );

        $command->handle();
    }
}