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
 * Class OrganizationViewTest
 * @package Tests\Feature\Http\Organization
 */
class OrganizationViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(Organization::class)->create();
        $response = $this->json('GET', '/v1/organizations/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(Organization::class)->create();
            $response = $this->json('GET', '/v1/organizations/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::MANAGER);
        /** @var Organization $model */
        $model = factory(Organization::class)->create([
            'id'    =>  1,
        ]);
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
            'organization_id' => $model->id,
        ]);

        $response = $this->json('GET', '/v1/organizations/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/organizations/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/organizations/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}