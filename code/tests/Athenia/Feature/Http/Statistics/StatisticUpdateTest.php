<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Statistics;

use App\Models\Role;
use App\Models\Statistics\Statistic;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class StatisticUpdateTest
 * @package Tests\Athenia\Feature\Http\Statistics
 */
class StatisticUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/statistics/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $statistic = Statistic::factory()->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR, Role::SUPPORT_STAFF]) as $role) {
            $this->actAs($role);
            $statistic = Statistic::factory()->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful()
    {
        $this->actAs(Role::SUPER_ADMIN);

        /** @var Statistic $statistic */
        $statistic = Statistic::factory()->create([
            'name' => 'Test Stat',
        ]);

        $data = [
            'name' => 'Test Statistic',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);


        /** @var Statistic $updated */
        $updated = Statistic::find($statistic->id);

        $this->assertEquals('Test Statistic', $updated->name);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchSuccessfulNoFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $statistic = Statistic::factory()->create([
            'name' => 'Test Gift Pack',
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, []);

        $response->assertStatus(200);
    }

    public function testPatchFailsIncludingNotPresetFields()
    {
        $statistic = Statistic::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, [
            'model' => 'character',
            'relation' => 'active'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'model' => ['The model field is not allowed or can not be set for this request.'],
                'relation' => ['The relation field is not allowed or can not be set for this request.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidStringFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidBooleanFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'public' => 'hi',
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'public' => ['The public field must be true or false.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidArrayFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'statistic_filters' => 'hi',
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'statistic_filters' => ['The statistic filters must be an array.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidFilterArrayFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'statistic_filters' => [
                'ho'
            ],
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'statistic_filters.0' => ['The statistic_filters.0 must be an array.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidFilterRequiredFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'statistic_filters' => [
                []
            ],
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'statistic_filters.0.field' => ['The statistic_filters.0.field field is required.'],
                'statistic_filters.0.operator' => ['The statistic_filters.0.operator field is required.'],
                'statistic_filters.0.value' => ['The statistic_filters.0.value field is required.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidFilterStringFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'statistic_filters' => [
                [
                    'field' => 1,
                    'operator' => 1,
                    'value' => 1,
                ]
            ],
        ];

        $statistic = Statistic::factory()->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $statistic->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'statistic_filters.0.field' => ['The statistic_filters.0.field must be a string.'],
                'statistic_filters.0.operator' => ['The statistic_filters.0.operator must be a string.'],
                'statistic_filters.0.value' => ['The statistic_filters.0.value must be a string.'],
            ]
        ]);
    }
} 