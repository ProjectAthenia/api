<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Subscription;

use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserSubscriptionUpdateTest
 * @package Tests\Feature\Http\User\Subscription
 */
class UserSubscriptionUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->user = User::factory()->create();

        $this->path.= $this->user->id . '/subscriptions/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserThanRouteBlocked()
    {
        $this->actAs(Role::APP_USER);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserThanSubscriptionBlocked()
    {
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create();
        $response = $this->json('PATCH', $this->path . $subscription->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
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
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $subscription->id, [
            'membership_plan_rate_id' => 32,
            'is_trial' => false,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate id field is not allowed or can not be set for this request.'],
                'is_trial' => ['The is trial field is not allowed or can not be set for this request.'],
            ],
        ]);
    }

    public function testUpdateFailsInvalidBooleanField()
    {
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
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

    public function testUpdateFailsInvalidIntegerFields()
    {
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
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

    public function testUpdateFailsInvalidModelFields()
    {
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
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

    public function testUpdateFailsPaymentMethodNotOwnedByUser()
    {
        $paymentMethod = PaymentMethod::factory()->create();
        $this->actingAs($this->user);
        $subscription = Subscription::factory()->create([
            'subscriber_id' => $this->user->id,
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
