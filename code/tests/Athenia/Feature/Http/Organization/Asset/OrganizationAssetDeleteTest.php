<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Organization\Asset;

use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodDeleteTest
 * @package Tests\Athenia\Feature\Http\Organization\Asset
 */
final class OrganizationAssetDeleteTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organization = Organization::factory()->create();
        $this->path.= $this->organization->id . '/assets/';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $asset = Asset::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked(): void
    {
        $asset = Asset::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }


    public function testDeleteSuccessful(): void
    {
        $asset = Asset::factory()->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);

        $this->actAsUser();

        OrganizationManager::factory()->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(204);

        $this->assertCount(0, Asset::all());
    }
}
