<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\AssetRepository;
use App\Models\Asset;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class AssetRepositoryTest
 * @package Tests\Integration\Repositories
 */
final class AssetRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var AssetRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new AssetRepository(
            new Asset(),
            $this->getGenericLogMock(),
            $this->app->make('filesystem'),
            'http://localhost',
            '/storage',
        );
    }

    public function testFindAllSuccess(): void
    {
        Asset::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findOrFail(54);
    }

    public function testCreateSuccess(): void
    {
        $user = User::factory()->create();
        /** @var Asset $asset */
        $asset = $this->repository->create([
            'url' => 'a url',
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertEquals('a url', $asset->url);
        $this->assertEquals($asset->owner_id, $user->id);
        $this->assertEquals($asset->owner_type, 'user');
    }

    public function testUpdateSuccess(): void
    {
        $asset = Asset::factory()->create();

        $this->repository->update($asset, [
            'url' => 'a new url',
        ]);

        $this->assertEquals('a new url', $asset->url);
    }

    public function testDeleteFails(): void
    {
        $asset = Asset::factory()->create();

        $this->repository->delete($asset);

        $this->assertNull(Asset::find($asset->id));
    }
}
