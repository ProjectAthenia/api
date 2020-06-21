<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Asset;

use App\Models\Asset;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\Organization\Organization;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationAssetUpdateTest
 * @package Tests\Feature\Http\Organization\Asset
 */
class OrganizationAssetUpdateTest extends TestCase
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

    public function testNotLoggedInOrganizationBlocked()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testNotRelatedToOrganizationBlocked()
    {
        $this->actAs(Role::APP_USER);
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testDifferentOrganizationThanAssetBlocked()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $asset = factory(Asset::class)->create();
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
            'name' => 'A Name',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'name' => 'A New Name',
        ]);

        $response->assertStatus(200);
        /** @var Asset $updated */
        $updated = Asset::find($asset->id);
        $this->assertEquals('A New Name', $updated->name);
    }

    public function testFailsNotPresentFieldsPresent()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'file_contents' => 'regoijer',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'file_contents' => ['The file contents field is not allowed or can not be set for this request.'],
            ],
        ]);
    }

    public function testFailsInvalidStringFields()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'organization_id' => $this->organization->id,
            'role_id' => Role::MANAGER,
            'user_id' => $this->actingAs->id,
        ]);
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->organization->id,
            'owner_type' => 'organization',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'name' => 45,
            'caption' => 45,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'name' => ['The name must be a string.'],
                'caption' => ['The caption must be a string.'],
            ],
        ]);
    }
}