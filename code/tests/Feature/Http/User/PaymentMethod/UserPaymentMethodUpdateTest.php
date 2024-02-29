<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\PaymentMethod;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodUpdateTest
 * @package Tests\Feature\Http\User\PaymentMethod
 */
final class UserPaymentMethodUpdateTest extends TestCase
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
        $this->path.= $this->user->id . '/payment-methods/';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);
        $response = $this->json('PUT', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actAsUser();

        $response = $this->json('PUT', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testUserDoesNotOwnPaymentMethodBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->json('PUT', $this->path . $paymentMethod->id, [
            'default' => false,
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'default' => false,
        ]);
    }

    public function testUpdateFailsNotAllowedFieldsPresent(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->json('PUT', $this->path . $paymentMethod->id, [
            'token' => 'hi',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'token' => ['The token field is not allowed or can not be set for this request.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidBooleanFields(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->json('PUT', $this->path . $paymentMethod->id, [
            'default' => 'hello',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'default' => ['The default field must be true or false.'],
            ]
        ]);
    }
}
