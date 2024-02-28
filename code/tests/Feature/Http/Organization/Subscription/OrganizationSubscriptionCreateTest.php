<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Subscription;

use App\Contracts\Services\StripePaymentServiceContract;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserSubscriptionCreateTest
 * @package Tests\Feature\Http\Organization\Subscription
 */
class OrganizationSubscriptionCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/organizations/';

    /**
     * @var Organization
     */
    private $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organization = Organization::factory()->create();

        $this->path.= $this->organization->id . '/subscriptions';
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testNoNonOrganizationManagerUserBlocked()
    {
        $this->actAsUser();

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->app->bind(StripePaymentServiceContract::class, function () {
            $mock = mock(StripePaymentServiceContract::class);

            $mock->shouldReceive('createPayment')->once();

            return $mock;
        });

        $membershipPlanRate = MembershipPlanRate::factory()->create([
            'active' => true,
        ]);
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $response = $this->json('POST', $this->path, [
            'membership_plan_rate_id' => $membershipPlanRate->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertStatus(201);

        /** @var Subscription $subscription */
        $subscription = Subscription::first();

        $this->assertEquals($subscription->membership_plan_rate_id, $membershipPlanRate->id);
        $this->assertEquals($subscription->payment_method_id, $paymentMethod->id);
        $this->assertEquals($subscription->subscriber_id, $this->organization->id);
        $this->assertEquals('organization', $subscription->subscriber_type);
    }

    public function testCreateFailsWhenStripeFails()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->app->bind(StripePaymentServiceContract::class, function () {
            $mock = mock(StripePaymentServiceContract::class);

            $mock->shouldReceive('createPayment')->once()->andThrow(new \Exception());

            return $mock;
        });

        $membershipPlanRate = MembershipPlanRate::factory()->create([
            'active' => true,
        ]);
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization'
        ]);

        $response = $this->json('POST', $this->path, [
            'membership_plan_rate_id' => $membershipPlanRate->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertStatus(503);
        $response->assertJson([
            'message' => 'Unable to accept payments right now',
        ]);

        $this->assertNull(Subscription::first());
    }

    public function testCreateFailsWithoutRequiredFields()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate id field is required.'],
                'payment_method_id' => ['The payment method id field is required unless is trial is in true.'],
            ],
        ]);
    }

    public function testCreateFailsWithNotPresentFieldsPresent()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('POST', $this->path, [
            'cancel' => true,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'cancel' => ['The cancel field is not allowed or can not be set for this request.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidBooleanField()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('POST', $this->path, [
            'recurring' => 'hello',
            'is_trial' => 'hello',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'recurring' => ['The recurring field must be true or false.'],
                'is_trial' => ['The is trial field must be true or false.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidIntegerFields()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('POST', $this->path, [
            'membership_plan_rate_id' => 'hi',
            'payment_method_id' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate id must be an integer.'],
                'payment_method_id' => ['The payment method id must be an integer.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidModelFields()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('POST', $this->path, [
            'membership_plan_rate_id' => 3452,
            'payment_method_id' => 54,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The selected membership plan rate id is invalid.'],
                'payment_method_id' => ['The selected payment method id is invalid.'],
            ],
        ]);
    }

    public function testCreateFailsPurchasingInactiveRate()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $membershipPlanRate = MembershipPlanRate::factory()->create([
            'active' => false,
        ]);

        $response = $this->json('POST', $this->path, [
            'membership_plan_rate_id' => $membershipPlanRate->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate must be active for you to purchase it.'],
            ],
        ]);
    }

    public function testCreateFailsPaymentMethodNotOwnedByUser()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->json('POST', $this->path, [
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'payment_method_id' => ['This payment method does not belong to this user.'],
            ],
        ]);
    }
}
