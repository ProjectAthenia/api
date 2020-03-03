<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\OrganizationManager;

use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationOrganizationManagerDeleteTest
 * @package Tests\Feature\Http\Organization\OrganizationManager
 */
class OrganizationOrganizationManagerDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $route;

    public function setUp(): void
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

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(OrganizationManager::class)->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('DELETE', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(OrganizationManager::class)->create();
            $this->setupRoute($model->organization_id, $model->id);
            $response = $this->json('DELETE', $this->route);
            $response->assertStatus(403);
        }
    }

    public function testOrganizationManagerBlocked()
    {
        $this->actAs(Role::ORGANIZATION_MANAGER);

        $model = factory(OrganizationManager::class)->create([
            'role_id' => Role::ORGANIZATION_MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $response = $this->json('DELETE', $this->route);
        $response->assertStatus(403);
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);

        $model = factory(OrganizationManager::class)->create([
            'role_id' => Role::ORGANIZATION_ADMIN,
            'user_id' => $this->actingAs->id,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $response = $this->json('DELETE', $this->route);

        $response->assertStatus(204);
        $this->assertNull(OrganizationManager::find($model->id));
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, 'a');
        $response = $this->json('DELETE', $this->route)
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, '435');
        $response = $this->json('DELETE', $this->route)
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}