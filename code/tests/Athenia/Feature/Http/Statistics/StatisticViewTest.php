<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Statistics;

use App\Athenia\Models\Role;
use App\Athenia\Models\Statistics\Statistic;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class StatisticViewTest
 * @package Tests\Athenia\Feature\Http\Statistics
 */
class StatisticViewTest extends TestCase
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
        $response = $this->json('GET', '/v1/statistics/' . $model->id);
        $response->assertStatus(403);
    }

    public function testInvalidRoleUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR, Role::SUPPORT_STAFF]) as $role) {
            $this->actAs($role);
            $model = Statistic::factory()->create();
            $response = $this->json('GET', '/v1/statistics/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::CONTENT_EDITOR);
        /** @var Statistic $model */
        $model = Statistic::factory()->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/statistics/1?expands[statisticFilters]=*');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::CONTENT_EDITOR);
        $response = $this->json('GET', '/v1/statistics/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::CONTENT_EDITOR);
        $response = $this->json('GET', '/v1/statistics/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
} 