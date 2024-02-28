<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Models\User\ProfileImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit\Models\User
 */
class UserTest extends TestCase
{
    public function testAssets(): void
    {
        $user = new User();
        $relation = $user->assets();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('assets.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testBallotCompletions(): void
    {
        $user = new User();
        $relation = $user->ballotCompletions();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('ballot_completions.user_id', $relation->getQualifiedForeignKeyName());
    }

    public function testCreatedArticles(): void
    {
        $user = new User();
        $relation = $user->createdArticles();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('articles.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testCreatedIterations(): void
    {
        $user = new User();
        $relation = $user->createdIterations();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('article_iterations.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testMessages(): void
    {
        $user = new User();
        $relation = $user->messages();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('messages.to_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOrganizationManagers(): void
    {
        $user = new User();
        $relation = $user->organizationManagers();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('organization_managers.user_id', $relation->getQualifiedForeignKeyName());
    }

    public function testPayments(): void
    {
        $user = new User();
        $relation = $user->payments();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('payments.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testPaymentMethods(): void
    {
        $user = new User();
        $relation = $user->paymentMethods();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('payment_methods.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testProfileImage(): void
    {
        $model = new User();

        $relation = $model->profileImage();

        $this->assertEquals('users.profile_image_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('assets.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testResource(): void
    {
        $user = new User();
        $relation = $user->resource();

        $this->assertEquals('resources.resource_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('resources.resource_type', $relation->getQualifiedMorphType());
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
    }

    public function testRoles(): void
    {
        $role = new User();
        $relation = $role->roles();

        $this->assertEquals('role_user', $relation->getTable());
        $this->assertEquals('role_user.user_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('role_user.role_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
    }

    public function testSubscriptions(): void
    {
        $user = new User();
        $relation = $user->subscriptions();

        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('subscriptions.subscriber_id', $relation->getQualifiedForeignKeyName());
    }

    public function testThreads(): void
    {
        $model = new User();
        $relation = $model->threads();

        $this->assertEquals('thread_user', $relation->getTable());
        $this->assertEquals('users.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('thread_user.user_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('thread_user.thread_id', $relation->getQualifiedRelatedPivotKeyName());
    }

    public function testGetProfileImageUrlAttribute(): void
    {
        $user = new User([
            'profileImage' => new ProfileImage([
                'url' => 'http://test.test/test.jpg',
            ]),
        ]);

        $this->assertEquals('http://test.test/test.jpg', $user->profile_image_url);
    }

    public function testGetJWTIdentifier(): void
    {
        $user = new User();
        $user->id = 4352;

        $this->assertEquals(4352, $user->getJWTIdentifier());
    }

    public function testGetJWTCustomClaims(): void
    {
        $user = new User();

        $this->assertEquals([], $user->getJWTCustomClaims());
    }

    public function testCurrentSubscription(): void
    {
        $noSubscriptionsUser = new User([
            'subscriptions' => new Collection([
            ]),
        ]);

        $this->assertNull($noSubscriptionsUser->currentSubscription());

        $subscription = new Subscription([
            'expires_at' => null,
            'membershipPlanRate' => new MembershipPlanRate([
                'membershipPlan' => new MembershipPlan([
                    'duration' => MembershipPlan::DURATION_LIFETIME,
                ]),
            ]),
        ]);
        $lifetimeSubscriptionUser = new User([
            'subscriptions' => new Collection([
                $subscription,
            ]),
        ]);

        $this->assertEquals($subscription, $lifetimeSubscriptionUser->currentSubscription());

        $subscription = new Subscription([
            'expires_at' => (new Carbon())->addMonth(),
            'membershipPlanRate' => new MembershipPlanRate([
                'membershipPlan' => new MembershipPlan([
                    'duration' => MembershipPlan::DURATION_YEAR,
                ]),
            ]),
        ]);
        $activeSubscriptionUser = new User([
            'subscriptions' => new Collection([
                $subscription,
            ]),
        ]);

        $this->assertEquals($subscription, $activeSubscriptionUser->currentSubscription());

        $expiredSubscriptionUser = new User([
            'subscriptions' => new Collection([
                new Subscription([
                    'expires_at' => (new Carbon())->subMonth(),
                    'membershipPlanRate' => new MembershipPlanRate([
                        'membershipPlan' => new MembershipPlan([
                            'duration' => MembershipPlan::DURATION_YEAR,
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $this->assertNull($expiredSubscriptionUser->currentSubscription());

        $withoutExpirationUser = new User([
            'subscriptions' => new Collection([
                new Subscription([
                    'membershipPlanRate' => new MembershipPlanRate([
                        'membershipPlan' => new MembershipPlan([
                            'duration' => MembershipPlan::DURATION_YEAR,
                        ]),
                    ]),
                ]),
            ]),
        ]);

        $this->assertNull($withoutExpirationUser->currentSubscription());
    }
}
