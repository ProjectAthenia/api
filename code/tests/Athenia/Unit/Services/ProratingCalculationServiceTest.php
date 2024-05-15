<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Athenia\Services\ProratingCalculationService;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class ProratingCalculationServiceTest
 * @package Tests\Unit\Services
 */
final class ProratingCalculationServiceTest extends TestCase
{
    public function testCalculateRemainingYearlyChargeWhenToDateIsBeforeToday(): void
    {
        $service = new ProratingCalculationService();

        $result = $service->calculateRemainingYearlyCharge(Carbon::now()->subDays(2), 10, 20);

        $this->assertEquals(0, $result);
    }

    public function testCalculateRemainingYearlyChargeWhenNewRateIsLessThanOldRate(): void
    {
        $service = new ProratingCalculationService();

        $result = $service->calculateRemainingYearlyCharge(Carbon::now()->addDays(44), 25, 20);

        $this->assertEquals(0, $result);
    }

    public function testCalculateRemainingYearlyChargeCalculatesExpectedAmount(): void
    {
        $service = new ProratingCalculationService();

        $result = $service->calculateRemainingYearlyCharge(Carbon::now()->addDays(45), 35, 75);

        $this->assertEquals(4.82, $result);
    }

    public function testCalculateMembershipUpgradeChargeWithNewLifetimeWithOldWithin3Months(): void
    {
        $service = new ProratingCalculationService();

        $currentSubscription = new Subscription([
            'subscribed_at' => Carbon::now()->subMonths(2),
            'membershipPlanRate' => new MembershipPlanRate([
                'cost' => 75,
            ]),
        ]);

        $newMembershipPlan = new MembershipPlan([
            'duration' => MembershipPlan::DURATION_LIFETIME,
            'currentRate' => new MembershipPlanRate([
                'active' => true,
                'cost' => 500,
            ]),
        ]);

        $result = $service->calculateMembershipUpgradeCharge($currentSubscription, $newMembershipPlan);

        $this->assertEquals(425, $result);
    }

    public function testCalculateMembershipUpgradeChargeWithNewLifetimeWithOldPast3Months(): void
    {
        $service = new ProratingCalculationService();

        $currentSubscription = new Subscription([
            'subscribed_at' => Carbon::now()->subMonths(5),
            'membershipPlanRate' => new MembershipPlanRate([
                'cost' => 75,
            ]),
        ]);

        $newMembershipPlan = new MembershipPlan([
            'duration' => MembershipPlan::DURATION_LIFETIME,
            'currentRate' => new MembershipPlanRate([
                'active' => true,
                'cost' => 500,
            ]),
        ]);

        $result = $service->calculateMembershipUpgradeCharge($currentSubscription, $newMembershipPlan);

        $this->assertEquals(500, $result);
    }

    public function testCalculateMembershipUpgradeChargeWithYearlyPlan(): void
    {
        $service = new ProratingCalculationService();

        $currentSubscription = new Subscription([
            'expires_at' => Carbon::now()->addDays(45),
            'membershipPlanRate' => new MembershipPlanRate([
                'cost' => 35,
            ]),
        ]);

        $newMembershipPlan = new MembershipPlan([
            'duration' => MembershipPlan::DURATION_YEAR,
            'currentRate' => new MembershipPlanRate([
                'active' => true,
                'cost' => 75,
            ]),
        ]);

        $result = $service->calculateMembershipUpgradeCharge($currentSubscription, $newMembershipPlan);

        $this->assertEquals(4.82, $result);
    }
}
