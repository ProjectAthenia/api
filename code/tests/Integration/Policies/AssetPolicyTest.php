<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\Asset;
use App\Models\User\User;
use App\Policies\AssetPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class AssetPolicyTest
 * @package Tests\Integration\Policies
 */
final class AssetPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllFails(): void
    {
        $policy = new AssetPolicy();

        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $this->assertFalse($policy->all($loggedInUser, $requestedUser));
    }

    public function testAllPasses(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();

        $this->assertTrue($policy->all($user, $user));
    }

    public function testCreateFails(): void
    {
        $policy = new AssetPolicy();

        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $this->assertFalse($policy->create($loggedInUser, $requestedUser));
    }

    public function testCreatePasses(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();

        $this->assertTrue($policy->create($user, $user));
    }

    public function testUpdateFailsUserMismatch(): void
    {
        $policy = new AssetPolicy();

        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();
        $asset = Asset::factory()->create([
            'owner_id' => $loggedInUser->id,
        ]);

        $this->assertFalse($policy->update($loggedInUser, $requestedUser, $asset));
    }

    public function testUpdateFailsAssetMismatch(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();
        $asset = Asset::factory()->create();

        $this->assertFalse($policy->update($user, $user, $asset));
    }

    public function testUpdatePasses(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();
        $asset = Asset::factory()->create([
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->update($user, $user, $asset));
    }

    public function testDeleteFailsUserMismatch(): void
    {
        $policy = new AssetPolicy();

        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();
        $asset = Asset::factory()->create([
            'owner_id' => $loggedInUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->delete($loggedInUser, $requestedUser, $asset));
    }

    public function testDeleteFailsAssetMismatch(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();
        $asset = Asset::factory()->create();

        $this->assertFalse($policy->delete($user, $user, $asset));
    }

    public function testDeletePasses(): void
    {
        $policy = new AssetPolicy();

        $user = User::factory()->create();
        $asset = Asset::factory()->create([
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->delete($user, $user, $asset));
    }
}
