<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\MembershipPlan;

use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanUpdateTest
 * @package Tests\Athenia\Feature\Http\MembershipPlan
 */
final class MembershipPlanUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/membership-plans/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $membershipPlan = MembershipPlan::factory()->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = MembershipPlan::factory()->create([
            'name' => 'Test Memberhip Plan',
        ]);

        $data = [
            'name' => 'Test Membership Plan',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);


        /** @var MembershipPlan $updated */
        $updated = MembershipPlan::find($membershipPlan->id);

        $this->assertEquals('Test Membership Plan', $updated->name);
    }

    public function testPatchNotFoundFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertSimilarJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertSimilarJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchSuccessfulNoFields(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $membershipPlan = MembershipPlan::factory()->create([
            'name' => 'Test Gift Pack',
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, []);

        $response->assertStatus(200);
    }

    public function testPatchFailsInvalidArrayFields(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'features' => 'hi',
        ];

        $membershipPlan = MembershipPlan::factory()->create([
            'name' => 'Test Gift Pack',
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'features' => ['The features must be an array.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidNumericFields(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, [
            'current_cost' => 'hi',
            'trial_period' => 'hi',
            'features' => ['hi'],
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'current_cost' => ['The current cost must be a number.'],
                'trial_period' => ['The trial period must be an integer.'],
                'features.0' => ['The features.0 must be a number.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidNumericMinimums(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, [
            'current_cost' => -1,
            'trial_period' => -1,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'current_cost' => ['The current cost must be at least 0.00.'],
                'trial_period' => ['The trial period must be at least 0.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidStringFields(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
            'description' => 5435,
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
                'description' => ['The description must be a string.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidBooleanFields(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'default' => 'hello',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'default' => ['The default field must be true or false.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidModelFields(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, [
            'features' => [1425]
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'features.0' => ['The selected features.0 is invalid.'],
            ]
        ]);
    }

    public function testPatchFailsTooLongFields(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => str_repeat('a', 121),
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name may not be greater than 120 characters.'],
            ]
        ]);
    }

    public function testPatchFailsProtectedFieldsPresent(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'entity_type' => 'hi',
            'duration' => 'hi',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'entity_type' => ['The entity type field is not allowed or can not be set for this request.'],
                'duration' => ['The duration field is not allowed or can not be set for this request.'],
            ]
        ]);
    }
}
