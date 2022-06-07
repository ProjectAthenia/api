<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Ballot;

use App\Models\Vote\Ballot;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class BallotViewTest
 * @package Tests\Ballot\Http\Ballot
 */
class BallotViewTest extends TestCase
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
        $model = Ballot::factory()->create();
        $response = $this->json('GET', '/v1/ballots/' . $model->id);
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::SUPER_ADMIN);
        /** @var Ballot $model */
        $model = Ballot::factory()->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/ballots/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/ballots/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('GET', '/v1/ballots/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
