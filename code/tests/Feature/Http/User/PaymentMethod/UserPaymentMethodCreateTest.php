<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\PaymentMethod;

use App\Contracts\Services\StripeCustomerServiceContract;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodCreateTest
 * @package Tests\Feature\Http\User\PaymentMethod
 */
class UserPaymentMethodCreateTest extends TestCase
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

        $this->path.= $this->user->id . '/payment-methods';
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked()
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actingAs($this->user);

        /** @var StripeCustomerServiceContract|CustomMockInterface $stripeCustomerService */
        $stripeCustomerService = $this->mock(StripeCustomerServiceContract::class);

        $this->app->bind(StripeCustomerServiceContract::class, function() use ($stripeCustomerService) {
            return $stripeCustomerService;
        });

        $stripeCustomerService->shouldReceive('createPaymentMethod')->once()
            ->with(\Mockery::on(function(User $user) {
                $this->assertEquals($user->id, $this->user->id);
                return true;
            }), 'test_token')->andReturn(new PaymentMethod([
                'payment_method_key' => 'test_key',
                'payment_method_type' => 'test_type',
            ]));

        $response = $this->json('POST', $this->path, [
            'token' => 'test_token',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'payment_method_key' => 'test_key',
            'payment_method_type' => 'test_type',
        ]);
    }

    public function testCreateFailsRequiredFieldsNotPresent()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'token' => ['The token field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'token' => 1,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'token' => ['The token must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsStringsTooLong()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'token' => str_repeat('a', 121),
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'token' => ['The token may not be greater than 120 characters.'],
            ]
        ]);
    }
}