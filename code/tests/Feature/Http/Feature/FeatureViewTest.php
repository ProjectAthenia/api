<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Feature;

use App\Models\Feature;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class FeatureViewTest
 * @package Tests\Feature\Http\Feature
 */
final class FeatureViewTest extends TestCase
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
        $model = Feature::factory()->create();
        $response = $this->json('GET', '/v1/features/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = Feature::factory()->create();
            $response = $this->json('GET', '/v1/features/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAs(Role::SUPER_ADMIN);
        /** @var Feature $model */
        $model = Feature::factory()->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/features/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/features/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/features/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
