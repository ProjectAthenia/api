<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\OrganizationManager;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationUpdateTest
 * @package Tests\Feature\Http\Organization\OrganizationManager
 */
class OrganizationOrganizationManagerUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $route;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    /**
     * Sets up the proper route for the request
     *
     * @param int $organizationId
     * @param int $organizationManagerId
     */
    private function setupRoute(int $organizationId, $organizationManagerId)
    {
        $this->route = '/v1/organizations/' . $organizationId . '/organization-managers/' . $organizationManagerId;
    }

    public function testOrganizationNotFound()
    {
        $this->setupRoute(4523, 345);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(404);
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = OrganizationManager::factory()->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = OrganizationManager::factory()->create();
            $this->setupRoute($model->organization_id, $model->id);
            $response = $this->json('PUT', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testNotUserNotOrganizationAdminBlocked()
    {
        $this->actAs(Role::MANAGER);
        $organization = Organization::factory()->create();
        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);
        $model = OrganizationManager::factory()->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $organization = Organization::factory()->create();
        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $model = OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'role_id' => Role::MANAGER,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $properties = [
            'role_id' => Role::ADMINISTRATOR,
        ];

        $response = $this->json('PUT', $this->route, $properties);

        $response->assertStatus(200);

        /** @var OrganizationManager $updated */
        $updated = OrganizationManager::find($model->id);

        $this->assertEquals( Role::ADMINISTRATOR, $updated->role_id);
    }

    public function testUpdateFailsMissingRequiredFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $response = $this->json('PUT', $this->route);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'role_id' => ['The role id field is required.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidNumericalFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $data = [
            'role_id' => 'weg',
        ];

        $response = $this->json('PUT', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'role_id' => ['The role id must be an integer.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidRoleId()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $data = [
            'role_id' => Role::SUPER_ADMIN,
        ];

        $response = $this->json('PUT', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'role_id' => ['The selected role id is invalid.'],
            ]
        ]);
    }
}
