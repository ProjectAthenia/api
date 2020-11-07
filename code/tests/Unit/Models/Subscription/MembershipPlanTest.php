<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Subscription;

use App\Models\Subscription\MembershipPlan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class MembershipPlanTest
 * @package Tests\Unit\Models\Subscription
 */
class MembershipPlanTest extends TestCase
{
    public function testFeatures()
    {
        $role = new MembershipPlan();
        $relation = $role->features();

        $this->assertEquals('feature_membership_plan', $relation->getTable());
        $this->assertEquals('feature_membership_plan.membership_plan_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('feature_membership_plan.feature_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertEquals('membership_plans.id', $relation->getQualifiedParentKeyName());
    }

    public function testMembershipPlanRates()
    {
        $user = new MembershipPlan();
        $relation = $user->membershipPlanRates();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('membership_plans.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('membership_plan_rates.membership_plan_id', $relation->getQualifiedForeignKeyName());
    }
}
