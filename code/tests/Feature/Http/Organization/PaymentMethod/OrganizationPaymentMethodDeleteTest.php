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
 * Class OrganizationPaymentMethodDeleteTest
 * @package Tests\Feature\Http\Organization\PaymentMethod
 */
class OrganizationPaymentMethodDeleteTest extends TestCase
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
        $this->path.= $this->organization->id . '/payment-methods/';
    }

    public function testNotLoggedInOrganizationBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testNotAdministratorBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testOrganizationDoesNotOwnPaymentMethodBlocked()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(403);
    }

    public function testDeleteSuccessful()
    {
        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $response = $this->json('DELETE', $this->path . $paymentMethod->id);

        $response->assertStatus(204);

        $this->assertCount(0, PaymentMethod::all());
    }
}