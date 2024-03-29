<?php
declare(strict_types=1);

namespace Feature\Http\Organization\Collection;

use App\Contracts\Services\StripeCustomerServiceContract;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
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
class OrganizationCollectionCreateTest extends TestCase
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

        $this->organization = Organization::factory()->create();

        $this->path.= $this->organization->id . '/collections';
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testNotPartOfOrganizationUserRoleBlocked()
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $data = [
            'name' => 'My Collection',
            'is_public' => false,
        ];
        $response = $this->json('POST', $this->path, $data);

        $response->assertStatus(201);

        $response->assertJson($data);
    }

    public function testCreateFailsRequiredFieldsNotPresent()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'is_public' => ['The is public field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

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

    public function testCreateFailsInvalidBooleanFields()
    {
        $this->actAsUser();

        OrganizationManager::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

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
