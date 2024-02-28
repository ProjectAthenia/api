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

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $model = Organization::factory()->create();
        $response = $this->json('GET', '/v1/organizations/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = Organization::factory()->create();
            $response = $this->json('GET', '/v1/organizations/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAs(Role::MANAGER);
        /** @var Organization $model */
        $model = Organization::factory()->create([
            'id'    =>  1,
        ]);
        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
            'organization_id' => $model->id,
        ]);

        $response = $this->json('GET', '/v1/organizations/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails(): void
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/organizations/1')
            ->assertSimilarJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/organizations/a')
            ->assertSimilarJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
