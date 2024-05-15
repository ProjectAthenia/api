<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Roles;

use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class RolesIndexTest
 * @package Tests\Athenia\Feature\Http\Roles
 */
final class RolesIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string the base path
     */
    protected $path = '/v1/roles';
    
    protected function setUp(): void
    {
        parent::setUp(); 
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testIndexBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $user = $this->getUserOfRole($role);
            $this->actingAs($user);

            $response = $this->json('GET', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testIndexSuccess(): void
    {
        $this->actingAs($this->getUserOfRole(Role::SUPER_ADMIN));
        $total = Role::count();

        // first page
        $response = $this->json('GET', $this->path);

        $response->assertStatus(200);
        $response->assertJson([
            'total' => $total,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => $total > 10 ? 10 : $total,
            'last_page' => (int) ceil($total /10 )
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Role())->toArray())
                ]
            ]);
    }
}
