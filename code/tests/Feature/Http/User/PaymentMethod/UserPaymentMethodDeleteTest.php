<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\PaymentMethod;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodDeleteTest
 * @package Tests\Feature\Http\User\PaymentMethod
 */
class UserPaymentMethodDeleteTest extends TestCase
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
        $this->path.= $this->user->id . '/payment-methods/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->user->id,
        ]);
        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actAsUser();

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testUserDoesNotOwnPaymentMethodBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testDeleteSuccessful()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(204);

        $this->assertCount(0, PaymentMethod::all());
    }
}