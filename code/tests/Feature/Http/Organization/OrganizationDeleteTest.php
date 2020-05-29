<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationDeleteTest
 * @package Tests\Feature\Http\Organization
 */
class OrganizationDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    private $route = '/v1/organizations/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(Organization::class)->create();
        $response = $this->json('DELETE', $this->route . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(Organization::class)->create();
            $response = $this->json('DELETE', $this->route . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testOrganizationManagerBlocked()
    {
        $this->actAs(Role::MANAGER);

        $model = factory(Organization::class)->create();

        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
            'organization_id' => $model->id,
        ]);

        $response = $this->json('DELETE', $this->route . $model->id);
        $response->assertStatus(403);
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::ADMINISTRATOR);

        $model = factory(Organization::class)->create();

        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
            'organization_id' => $model->id,
        ]);

        $response = $this->json('DELETE', $this->route . $model->id);

        $response->assertStatus(204);
        $this->assertNull(Organization::find($model->id));
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', $this->route . 'a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', $this->route . '1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}