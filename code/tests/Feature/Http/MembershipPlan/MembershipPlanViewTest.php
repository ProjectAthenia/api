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
final class MembershipPlanViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $model = MembershipPlan::factory()->create();
        $response = $this->json('GET', '/v1/membership-plans/' . $model->id);
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAs(Role::APP_USER);
        /** @var MembershipPlan $model */
        $model = MembershipPlan::factory()->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/membership-plans/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails(): void
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/membership-plans/1')
            ->assertSimilarJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/membership-plans/a')
            ->assertSimilarJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
