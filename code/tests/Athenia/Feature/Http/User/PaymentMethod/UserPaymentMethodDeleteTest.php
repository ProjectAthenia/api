<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\User\PaymentMethod;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodDeleteTest
 * @package Tests\Athenia\Feature\Http\User\PaymentMethod
 */
final class UserPaymentMethodDeleteTest extends TestCase
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
        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actAsUser();

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testUserDoesNotOwnPaymentMethodBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testDeleteSuccessful(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(204);

        $this->assertCount(0, PaymentMethod::all());
    }
}
