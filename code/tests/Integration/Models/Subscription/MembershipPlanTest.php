<?php
declare(strict_types=1);

namespace Tests\Integration\Models\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use Carbon\Carbon;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MembershipPlanTest
 * @package Tests\Integration\Models\Subscription
 */
final class MembershipPlanTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testCurrentCostAttribute(): void
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = MembershipPlan::factory()->create();

        MembershipPlanRate::factory()->create([
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 42.12,
            'active' => true,
            'created_at' => Carbon::now()->subDay(),
        ]);
        MembershipPlanRate::factory()->create([
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 65.43,
            'active' => true,
            'created_at' => Carbon::now(),
        ]);
        MembershipPlanRate::factory()->create([
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 12.43,
            'active' => false,
            'created_at' => Carbon::now()->addDay(),
        ]);

        $this->assertEquals(65.43, $membershipPlan->current_cost);
    }

    public function testCurrentRateIdAttribute(): void
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = MembershipPlan::factory()->create();

        MembershipPlanRate::factory()->create([
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 42.12,
            'active' => true,
            'created_at' => Carbon::now()->subDay(),
        ]);
        MembershipPlanRate::factory()->create([
            'id' => 6,
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 65.43,
            'active' => true,
            'created_at' => Carbon::now(),
        ]);
        MembershipPlanRate::factory()->create([
            'membership_plan_id' => $membershipPlan->id,
            'cost' => 12.43,
            'active' => false,
            'created_at' => Carbon::now()->addDay(),
        ]);

        $this->assertEquals(6, $membershipPlan->current_rate_id);
    }
}
