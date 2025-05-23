<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Statistic;

use App\Models\Role;
use App\Models\Statistic\Statistic;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class StatisticDeleteTest
 * @package Tests\Athenia\Feature\Http\Statistics
 */
class StatisticDeleteTest extends TestCase
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
        $model = Statistic::factory()->create();
        $response = $this->json('DELETE', '/v1/statistics/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR, Role::SUPPORT_STAFF]) as $role) {
            $this->actAs($role);
            $model = Statistic::factory()->create();
            $response = $this->json('DELETE', '/v1/statistics/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $model = Statistic::factory()->create();

        $response = $this->json('DELETE', '/v1/statistics/' . $model->id);

        $response->assertStatus(204);
        $this->assertEquals(0, Statistic::count());
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $response = $this->json('DELETE', '/v1/statistics/a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $response = $this->json('DELETE', '/v1/statistics/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
} 