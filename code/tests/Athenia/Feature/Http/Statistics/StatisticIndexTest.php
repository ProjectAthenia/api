<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Statistics;

use App\Models\Role;
use App\Models\Statistics\Statistic;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class StatisticIndexTest
 * @package Tests\Athenia\Feature\Http\Statistics
 */
class StatisticIndexTest extends TestCase
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
        $response = $this->json('GET', '/v1/statistics');
        dump($response);
        $response->assertStatus(403);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/statistics');
        dump($response);
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::APP_USER);
        Statistic::factory()->count(15)->create();

        // first page
        $response = $this->json('GET', '/v1/statistics');
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
                    '*' =>  array_keys((new Statistic())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // second page
        $response = $this->json('GET', '/v1/statistics?page=2');
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
                    '*' =>  array_keys((new Statistic())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // page with limit
        $response = $this->json('GET', '/v1/statistics?page=2&limit=5');
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
                    '*' =>  array_keys((new Statistic())->toArray())
                ]
            ]);
        $response->assertStatus(200);
    }
} 