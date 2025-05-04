<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Statistics;

use App\Models\Statistics\Statistic;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;
use Illuminate\Support\Facades\DB;
use App\Http\V1\Controllers\StatisticController;

/**
 * Class StatisticCreateTest
 * @package Tests\Athenia\Feature\Http\Statistics
 */
class StatisticCreateTest extends TestCase
{
    use DatabaseSetupTrait, RolesTesting;

    private $route = '/v1/statistics';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->route);

        $response->assertStatus(403);
    }

    public function testNotAuthorizedUserBlocked()
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->route);

        $response->assertStatus(403);
    }

    public function testCreateSuccessWithoutStatisticFilters()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $properties = [
            'name' => 'Test Statistic',
            'model' => 'collection',
            'relation' => 'collectionItems',
            'public' => true,
        ];

        $response = $this->json('POST', $this->route, $properties);
        $response->assertStatus(201);
        $response->assertJsonFragment($properties);
    }

    public function testCreateSuccessWithStatisticFilters()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $properties = [
            'name' => 'Test Statistic',
            'model' => 'collection',
            'relation' => 'collectionItems',
            'public' => true,
            'statistic_filters' => [
                [
                    'field' => 'active',
                    'operator' => '=',
                    'value' => '1',
                ],
            ],
        ];

        $response = $this->json('POST', $this->route, $properties);
        $response->assertStatus(201);
        unset($properties['statistic_filters']);
        $response->assertJsonFragment($properties);

        /** @var Statistic $created */
        $created = Statistic::first();
        $this->assertCount(1, $created->statisticFilters);
    }

    public function testCreateFailsValidation()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $response = $this->json('POST', $this->route, [
            'name' => '',
            'model' => '',
            'relation' => '',
            'public' => 'yes',
            'statistic_filters' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors([
            'name' => ['The name field is required.'],
            'model' => ['The model field is required.'],
            'relation' => ['The relation field is required.'],
            'public' => ['The public field must be true or false.'],
            'statistic_filters' => ['The statistic filters must be an array.'],
        ]);
    }

    public function testCreateFailsStatisticFilterValidation()
    {
        $this->actAs(Role::CONTENT_EDITOR);

        $response = $this->json('POST', $this->route, [
            'name' => 'Test',
            'model' => 'collection',
            'relation' => '',
            'statistic_filters' => [
                'not an array',
            ],
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors([
            'statistic_filters.0' => ['The statistic_filters.0 must be an array.'],
            'statistic_filters.0.field' => ['The statistic_filters.0.field field is required.'],
            'statistic_filters.0.operator' => ['The statistic_filters.0.operator field is required.'],
        ]);

        $response = $this->json('POST', $this->route, [
            'name' => 'Test',
            'model' => 'collection',
            'relation' => 'collectionItems',
            'statistic_filters' => [
                [],
            ],
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors([
            'statistic_filters.0.field' => ['The statistic_filters.0.field field is required.'],
            'statistic_filters.0.operator' => ['The statistic_filters.0.operator field is required.'],
        ]);

        $response = $this->json('POST', $this->route, [
            'name' => 'Test',
            'model' => 'collection',
            'relation' => 'collectionItems',
            'statistic_filters' => [
                [
                    'field' => 123,
                    'operator' => 456,
                    'value' => 789,
                ],
            ],
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors([
            'statistic_filters.0.field' => ['The statistic_filters.0.field must be a string.'],
            'statistic_filters.0.operator' => ['The statistic_filters.0.operator must be a string.'],
            'statistic_filters.0.value' => ['The statistic_filters.0.value must be a string.'],
        ]);
    }
} 