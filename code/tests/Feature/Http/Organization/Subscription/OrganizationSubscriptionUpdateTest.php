<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Subscription;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription\Subscription;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationSubscriptionUpdateTest
 * @package Tests\Feature\Http\Organization\Subscription
 */
class OrganizationSubscriptionUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/organizations/';

    /**
     * @var Organization
     */
    private $organizaion;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organizaion = factory(Organization::class)->create();

        $this->path.= $this->organizaion->id . '/subscriptions/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testDisconnectedUserBlocked()
    {
        $this->actAs(Role::APP_USER);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserThanSubscriptionBlocked()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create();
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testWrongRoleBlocked()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'cancel' => true,
        ]);

        $response->assertStatus(200);
        /** @var Subscription $updated */
        $updated = Subscription::find($subscription->id);
        $this->assertNotNull($updated->canceled_at);
    }

    public function testFailsNotPresentFieldsPresent()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'membership_plan_rate_id' => 32,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate id field is not allowed or can not be set for this request.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidBooleanField()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'recurring' => 'hello',
            'cancel' => 'hello',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'recurring' => ['The recurring field must be true or false.'],
                'cancel' => ['The cancel field must be true or false.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidIntegerFields()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'payment_method_id' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'payment_method_id' => ['The payment method id must be an integer.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidModelFields()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'payment_method_id' => 54,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'payment_method_id' => ['The selected payment method id is invalid.'],
            ],
        ]);
    }

    public function testCreateFailsPaymentMethodNotOwnedByUser()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organizaion->id,
        ]);
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $this->organizaion->id,
            'subscriber_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
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