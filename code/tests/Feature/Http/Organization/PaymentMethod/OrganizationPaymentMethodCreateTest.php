<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\PaymentMethod;

use App\Contracts\Services\StripeCustomerServiceContract;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Organization\Organization;
use App\Models\Role;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationPaymentMethodCreateTest
 * @package Tests\Feature\Http\Organization\PaymentMethod
 */
class OrganizationPaymentMethodCreateTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organization = factory(Organization::class)->create();

        $this->path.= $this->organization->id . '/payment-methods';
    }

    public function testNotLoggedInOrganizationBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        /** @var StripeCustomerServiceContract|CustomMockInterface $stripeCustomerService */
        $stripeCustomerService = $this->mock(StripeCustomerServiceContract::class);

        $this->app->bind(StripeCustomerServiceContract::class, function() use ($stripeCustomerService) {
            return $stripeCustomerService;
        });

        $stripeCustomerService->shouldReceive('createPaymentMethod')->once()
            ->with(\Mockery::on(function(Organization $organization) {
                $this->assertEquals($organization->id, $this->organization->id);
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
        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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
        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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
        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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