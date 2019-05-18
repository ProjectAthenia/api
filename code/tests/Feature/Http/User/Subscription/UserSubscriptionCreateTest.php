<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Subscription;

use App\Contracts\Services\StripePaymentServiceContract;
use App\Models\Payment\PaymentMethod;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserSubscriptionCreateTest
 * @package Tests\Feature\Http\User\Subscription
 */
class UserSubscriptionCreateTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->user = factory(User::class)->create();

        $this->path.= $this->user->id . '/subscriptions';
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actingAs($this->user);

        $this->app->bind(StripePaymentServiceContract::class, function () {
            $mock = mock(StripePaymentServiceContract::class);

            $mock->shouldReceive('createPayment')->once();

            return $mock;
        });

        $membershipPlanRate = factory(MembershipPlanRate::class)->create([
            'active' => true,
        ]);
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->user->id,
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
        $this->assertEquals($subscription->subscriber_id, $this->user->id);
    }

    public function testCreateFailsWhenStripeFails()
    {
        $this->actingAs($this->user);

        $this->app->bind(StripePaymentServiceContract::class, function () {
            $mock = mock(StripePaymentServiceContract::class);

            $mock->shouldReceive('createPayment')->once()->andThrow(new \Exception());

            return $mock;
        });

        $membershipPlanRate = factory(MembershipPlanRate::class)->create([
            'active' => true,
        ]);
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->user->id,
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
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'membership_plan_rate_id' => ['The membership plan rate id field is required.'],
                'payment_method_id' => ['The payment method id field is required.'],
            ],
        ]);
    }

    public function testCreateFailsWithNotPresentFieldsPresent()
    {
        $this->actingAs($this->user);

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
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'recurring' => 'hello',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'recurring' => ['The recurring field must be true or false.'],
            ],
        ]);
    }

    public function testCreateFailsInvalidIntegerFields()
    {
        $this->actingAs($this->user);

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
        $this->actingAs($this->user);

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
        $this->actingAs($this->user);

        $membershipPlanRate = factory(MembershipPlanRate::class)->create([
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
        $this->actingAs($this->user);

        $paymentMethod = factory(PaymentMethod::class)->create();

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