<?php
declare(strict_types=1);

namespace Tests\Feature\Organization\Asset;

use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationContactIndexTest
 * @package Tests\Feature\Organization\Asset
 */
class OrganizationAssetIndexTest extends TestCase
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

    public function testNotLoggedInOrganizationBlocked()
    {
        $organization = factory(Organization::class)->create();

        $response = $this->json('GET', $this->path . $organization->id . '/assets');

        $response->assertStatus(403);
    }

    public function tesNotOrganizationManagerBlocked()
    {
        $this->actAsUser();
        $organization = factory(Organization::class)->create();

        $response = $this->json('GET', $this->path . $organization->id . '/assets');

        $response->assertStatus(403);
    }

    public function testOrganizationNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/assets');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();
        $organization = factory(Organization::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $organization->id,
        ]);

        $response = $this->json('GET', $this->path. $organization->id . '/assets');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();
        $organization = factory(Organization::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $organization->id,
        ]);

        factory(Asset::class, 6)->create();
        factory(Asset::class, 15)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);
        factory(Asset::class, 3)->create([
            'owner_id' => $organization->id,
            'owner_type' => 'user',
        ]);

        // first page
        $response = $this->json('GET', $this->path . $organization->id . '/assets');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $organization->id . '/assets?page=2');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $organization->id . '/assets?page=2&limit=5');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);
    }
}