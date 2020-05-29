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
 * Class OrganizationOrganizationManagerIndexTest
 * @package Tests\Feature\Http\Organization\OrganizationManager
 */
class OrganizationOrganizationManagerIndexTest extends TestCase
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
     */
    private function setupRoute(int $organizationId)
    {
        $this->route = '/v1/organizations/' . $organizationId . '/organization-managers';
    }

    public function testNotLoggedInUserBlocked()
    {
        $organization = factory(Organization::class)->create();
        $this->setupRoute($organization->id);
        $response = $this->json('GET', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $organization = factory(Organization::class)->create();
            $this->setupRoute($organization->id);
            $response = $this->json('GET', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::MANAGER);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);
        $this->setupRoute($organization->id);
        factory(OrganizationManager::class, 14)->create([
            'organization_id' => $organization->id,
        ]);
        factory(OrganizationManager::class, 3)->create();

        // first page
        $response = $this->json('GET', $this->route);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Organization())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // second page
        $response = $this->json('GET', $this->route . '?page=2');
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 10,
            'from' => 11,
            'to' => 15,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Organization())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // page with limit
        $response = $this->json('GET', $this->route . '?page=2&limit=5');
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
            'last_page' => 3
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Organization())->toArray())
                ]
            ]);
        $response->assertStatus(200);
    }
}