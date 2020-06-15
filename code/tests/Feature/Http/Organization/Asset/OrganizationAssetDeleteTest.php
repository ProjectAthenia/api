<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Asset;

use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodDeleteTest
 * @package Tests\Feature\Http\Organization\Asset
 */
class OrganizationAssetDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/organizations/';

    /**
     * @var Organization
     */
    private $organization;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organization = factory(Organization::class)->create();
        $this->path.= $this->organization->id . '/assets/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }


    public function testDeleteSuccessful()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(204);

        $this->assertCount(0, Asset::all());
    }
}