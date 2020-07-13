<?php
declare(strict_types=1);

namespace Tests\Feature\Http\MembershipPlan\MembershipPlanRate;

use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanMembershipPlanRateIndexTest
 * @package Tests\Feature\Http\MembershipPlan\MembershipPlanRate
 */
class MembershipPlanMembershipPlanRateIndexTest extends TestCase
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
     * @param int $id
     */
    private function setupRoute(int $id)
    {
        $this->route = '/v1/membership-plans/' . $id . '/rates';
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(MembershipPlan::class)->create();
        $this->setupRoute($model->id);
        $response = $this->json('GET', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(MembershipPlan::class)->create();
            $this->setupRoute($model->id);
            $response = $this->json('GET', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testGetPaginationEmpty()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $model = factory(MembershipPlan::class)->create();
        $this->setupRoute($model->id);
        $response = $this->json('GET', $this->route);

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $model = factory(MembershipPlan::class)->create();
        $this->setupRoute($model->id);
        factory(MembershipPlanRate::class, 15)->create([
            'membership_plan_id' => $model->id,
        ]);
        factory(MembershipPlanRate::class, 3)->create();

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
                    '*' =>  array_keys((new MembershipPlanRate())->toArray())
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
                    '*' =>  array_keys((new MembershipPlanRate())->toArray())
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
                    '*' =>  array_keys((new MembershipPlanRate())->toArray())
                ]
            ]);
        $response->assertStatus(200);
    }
}