<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Collection;

use App\Models\Collection\Collection;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationSubscriptionIndexTest
 * @package Tests\Feature\Organization\Payment
 */
final class OrganizationCollectionIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/organizations/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInOrganizationBlocked(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->json('GET', $this->path . $organization->id . '/collections');

        $response->assertStatus(403);
    }

    public function tesNotOrganizationManagerBlocked()
    {
        $this->actAsUser();
        $organization = Organization::factory()->create();

        $response = $this->json('GET', $this->path . $organization->id . '/collections');

        $response->assertStatus(403);
    }

    public function testOrganizationNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/collections');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty(): void
    {
        $this->actAsUser();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $organization->id,
        ]);

        $response = $this->json('GET', $this->path. $organization->id . '/collections');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult(): void
    {
        $this->actAsUser();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $organization->id,
        ]);

        Collection::factory()->count( 6)->create();
        Collection::factory()->count( 15)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);
        Collection::factory()->count( 3)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'user',
        ]);

        // first page
        $response = $this->json('GET', $this->path . $organization->id . '/collections');
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
        $response = $this->json('GET', $this->path . $organization->id . '/collections?page=2');
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
        $response = $this->json('GET', $this->path . $organization->id . '/collections?page=2&limit=5');
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

    public function testDifferentUserDoesNotSeeNonPublicCollections(): void
    {
        $this->actAsUser();
        $organization = Organization::factory()->create();

        Collection::factory()->count( 15)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
            'is_public' => false,
        ]);
        Collection::factory()->count(3)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
            'is_public' => true,
        ]);

        $response = $this->json('GET', $this->path . $organization->id . '/collections');
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
