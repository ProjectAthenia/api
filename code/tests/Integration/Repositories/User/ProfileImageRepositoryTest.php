<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Exceptions\NotImplementedException;
use App\Models\Asset;
use App\Models\User\ProfileImage;
use App\Repositories\User\ProfileImageRepository;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ProfileImageRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
class ProfileImageRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ProfileImageRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ProfileImageRepository(
            new ProfileImage(),
            $this->getGenericLogMock(),
            $this->app->make('filesystem'),
            'http://localhost',
            '/storage',
        );
    }

    public function testFindAllFails()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findAll();
    }

    public function testFindOrFailSuccess()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findOrFail(54);
    }

    public function testCreateSuccess()
    {
        /** @var Asset $asset */
        $asset = $this->repository->create([
            'url' => 'a url',
        ]);

        $this->assertEquals('a url', $asset->url);
    }

    public function testUpdateFails()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->update(new Asset(), []);
    }

    public function testDeleteFails()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Asset());
    }
}