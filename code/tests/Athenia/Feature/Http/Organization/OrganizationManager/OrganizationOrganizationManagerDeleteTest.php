<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Organization\OrganizationManager;

use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationOrganizationManagerDeleteTest
 * @package Tests\Athenia\Feature\Http\Organization\OrganizationManager
 */
final class OrganizationOrganizationManagerDeleteTest extends TestCase
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

    public function testNotLoggedInUserBlocked(): void
    {
        $model = OrganizationManager::factory()->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('DELETE', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = OrganizationManager::factory()->create();
            $this->setupRoute($model->organization_id, $model->id);
            $response = $this->json('DELETE', $this->route);
            $response->assertStatus(403);
        }
    }

    public function testOrganizationManagerBlocked(): void
    {
        $this->actAs(Role::MANAGER);

        $model = OrganizationManager::factory()->create([
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $response = $this->json('DELETE', $this->route);
        $response->assertStatus(403);
    }

    public function testDeleteSingle(): void
    {
        $this->actAs(Role::ADMINISTRATOR);

        $model = OrganizationManager::factory()->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $response = $this->json('DELETE', $this->route);

        $response->assertStatus(204);
        $this->assertNull(OrganizationManager::find($model->id));
    }

    public function testDeleteSingleInvalidIdFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, 'a');
        $response = $this->json('DELETE', $this->route)
            ->assertSimilarJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, '435');
        $response = $this->json('DELETE', $this->route)
            ->assertSimilarJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}
