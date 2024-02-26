<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Collection;

use App\Models\Collection\Collection;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserSubscriptionIndexTest
 * @package Tests\Feature\User\Payment
 */
class UserCollectionIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        User::unsetEventDispatcher();
    }

    public function testNotLoggedInUserBlocked()
    {
        $user = User::factory()->create();

        $response = $this->json('GET', $this->path . $user->id . '/collections');

        $response->assertStatus(403);
    }

    public function testUserNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/collections');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/collections');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        Collection::factory()->count( 6)->create([
            'is_public' => true,
        ]);
        Collection::factory()->count( 15)->create([
            'owner_id' => $this->actingAs->id,
            'owner_type' => 'user',
            'is_public' => true,
        ]);
        Collection::factory()->count(3)->create([
            'owner_id' => $this->actingAs->id,
            'owner_type' => 'organization',
            'is_public' => true,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/collections');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Collection())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/collections?page=2');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Collection())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/collections?page=2&limit=5');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Collection())->toArray())
                ]
            ]);
    }

    public function testDifferentUserDoesNotSeeNonPublicCollections()
    {
        $this->actAsUser();
        $user = User::factory()->create();

        Collection::factory()->count( 15)->create([
            'owner_id' => $user->id,
            'owner_type' => 'user',
            'is_public' => false,
        ]);
        Collection::factory()->count(3)->create([
            'owner_id' => $user->id,
            'owner_type' => 'user',
            'is_public' => true,
        ]);

        $response = $this->json('GET', $this->path . $user->id . '/collections');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 3,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 3,
            'last_page' => 1
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Collection())->toArray())
                ]
            ]);
    }
}
