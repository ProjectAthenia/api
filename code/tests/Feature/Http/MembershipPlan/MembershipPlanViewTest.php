<?php
declare(strict_types=1);

namespace Tests\Feature\Http\MembershipPlan;

use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class MembershipPlanViewTest
 * @package Tests\Feature\Http\MembershipPlan
 */
class MembershipPlanViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(MembershipPlan::class)->create();
        $response = $this->json('GET', '/v1/membership-plans/' . $model->id);
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::APP_USER);
        /** @var MembershipPlan $model */
        $model = factory(MembershipPlan::class)->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/membership-plans/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/membership-plans/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/membership-plans/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}