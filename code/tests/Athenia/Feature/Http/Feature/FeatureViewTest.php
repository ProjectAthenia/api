<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Feature;

use App\Models\Feature;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class FeatureViewTest
 * @package Tests\Athenia\Feature\Http\Feature
 */
final class FeatureViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testGetSingleSuccess(): void
    {
        /** @var Feature $model */
        $model = Feature::factory()->create();

        $response = $this->json('GET', '/v1/features/' . $model->id);

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails(): void
    {
        $response = $this->json('GET', '/v1/features/13452')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails(): void
    {
        $response = $this->json('GET', '/v1/features/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
