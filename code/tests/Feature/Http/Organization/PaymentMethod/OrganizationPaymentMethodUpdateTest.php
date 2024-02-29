<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\PaymentMethod;

use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Organization\Organization;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationPaymentMethodCreateTest
 * @package Tests\Feature\Http\Organization\PaymentMethod
 */
final class OrganizationPaymentMethodUpdateTest extends TestCase
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

        $this->path.= $this->organization->id . '/payment-methods/';
    }

    public function testNotLoggedInOrganizationBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('PUT', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testNotAdministratorBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $response = $this->json('PUT', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testOrganizationDoesNotOwnPaymentMethodBlocked(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actAsUser();
        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('PUT', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

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
