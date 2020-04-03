<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class SubscriptionTest
 * @package Tests\Unit\Models\Subscription
 */
class SubscriptionTest extends TestCase
{
    public function testLineItems()
    {
        $model = new Subscription();
        $relation = $model->lineItems();

        $this->assertEquals('line_items.item_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('line_items.item_type', $relation->getQualifiedMorphType());
        $this->assertEquals('subscriptions.id', $relation->getQualifiedParentKeyName());
    }

    public function testMembershipPlanRate()
    {
        $model = new Subscription();
        $relation = $model->membershipPlanRate();

        $this->assertEquals('membership_plan_rates.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('subscriptions.membership_plan_rate_id', $relation->getQualifiedForeignKeyName());
    }

    public function testPayments()
    {
        $user = new Subscription();
        $relation = $user->payments();

        $this->assertEquals('subscriptions.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('line_items.item_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('line_items.payment_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertEquals('item_type', $relation->getMorphType());
    }

    public function testPaymentMethod()
    {
        $model = new Subscription();
        $relation = $model->paymentMethod();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('payment_methods.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('subscriptions.payment_method_id', $relation->getQualifiedForeignKeyName());
    }

    public function testSubscriber()
    {
        $model = new Subscription();
        $relation = $model->subscriber();

        $this->assertEquals('subscriptions.subscriber_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('subscriber_type', $relation->getMorphType());
    }

    public function testIsLifetime()
    {
        $yearSubscription = new Subscription([
            'membershipPlanRate' => new MembershipPlanRate([
                'membershipPlan' => new MembershipPlan([
                    'duration' => MembershipPlan::DURATION_YEAR,
                ]),
            ]),
        ]);

        $this->assertFalse($yearSubscription->isLifetime());

        $lifetimeSubscription = new Subscription([
            'membershipPlanRate' => new MembershipPlanRate([
                'membershipPlan' => new MembershipPlan([
                    'duration' => MembershipPlan::DURATION_LIFETIME,
                ]),
            ]),
        ]);

        $this->assertTrue($lifetimeSubscription->isLifetime());
    }

    public function testFormattedExpiresAt()
    {
        $subscription = new Subscription();
        $this->assertNull($subscription->formatted_expires_at);

        $subscription = new Subscription([
            'expires_at' => new Carbon('2018-02-12'),
        ]);
        $this->assertEquals('February 12th 2018', $subscription->formatted_expires_at);
    }

    public function testFormattedCost()
    {
        $subscription = new Subscription();
        $this->assertNull($subscription->formatted_cost);

        $subscription = new Subscription([
            'membershipPlanRate' => new MembershipPlanRate([
                'cost' => 1,
            ]),
        ]);
        $this->assertEquals('1.00', $subscription->formatted_cost);
    }
}