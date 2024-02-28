<?php
declare(strict_types=1);

namespace Tests\Feature\Organization\Subscription;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Subscription\Subscription;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationSubscriptionIndexTest
 * @package Tests\Feature\Organization\Subscription
 */
class OrganizationSubscriptionIndexTest extends TestCase
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

        $response = $this->json('GET', $this->path . $organization->id . '/subscriptions');

        $response->assertStatus(403);
    }

    public function tesNotOrganizationManagerBlocked()
    {
        $this->actAsUser();
        $organization = Organization::factory()->create();

        $response = $this->json('GET', $this->path . $organization->id . '/subscriptions');

        $response->assertStatus(403);
    }

    public function testOrganizationNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/subscriptions');

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

        $response = $this->json('GET', $this->path. $organization->id . '/subscriptions');

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

        Subscription::factory()->count(6)->create();
        Subscription::factory()->count(15)->create([
            'subscriber_id' => $organization->id,
            'subscriber_type' => 'organization',
        ]);
        Subscription::factory()->count(3)->create([
            'subscriber_id' => $organization->id,
            'subscriber_type' => 'user',
        ]);

        // first page
        $response = $this->json('GET', $this->path . $organization->id . '/subscriptions');
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
                    '*' =>  array_keys((new Subscription())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $organization->id . '/subscriptions?page=2');
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
                    '*' =>  array_keys((new Subscription())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $organization->id . '/subscriptions?page=2&limit=5');
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
                    '*' =>  array_keys((new Subscription())->toArray())
                ]
            ]);
    }
}
