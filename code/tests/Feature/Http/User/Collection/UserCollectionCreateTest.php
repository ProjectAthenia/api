<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Collection;

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
class UserCollectionCreateTest extends TestCase
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

        $this->path.= $this->user->id . '/collections';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $this->actingAs($this->user);

        $data = [
            'name' => 'My Collection',
            'is_public' => false,
        ];
        $response = $this->json('POST', $this->path, $data);

        $response->assertStatus(201);

        $response->assertJson($data);
    }

    public function testCreateFailsRequiredFieldsNotPresent(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'is_public' => ['The is public field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'name' => 1,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidBooleanFields(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'is_public' => 'hello',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'is_public' => ['The is public field must be true or false.'],
            ]
        ]);
    }
}
