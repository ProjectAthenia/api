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
class FeatureViewTest extends TestCase
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
        $model = factory(Feature::class)->create();
        $response = $this->json('GET', '/v1/features/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(Feature::class)->create();
            $response = $this->json('GET', '/v1/features/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::SUPER_ADMIN);
        /** @var Feature $model */
        $model = factory(Feature::class)->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/features/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/features/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/features/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
