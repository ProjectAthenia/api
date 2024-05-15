<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\RoleRepository;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class RoleRepositoryTest
 * @package Tests\Integration\Repositories
 */
final class RoleRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;
    
    /**
     * @var RoleRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        
        $this->repository = new RoleRepository(new Role(), $this->getGenericLogMock());
    }

    public function testFindAllSuccess(): void
    {
        $items = $this->repository->findAll([], [], [], [], 0);
        $this->assertCount(Role::count(), $items);
    }

    public function testUpdate(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->update(new Role(), []);
    }

    public function testDelete(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->delete(new Role());
    }

    public function testFindOrFail(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreate(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->create([]);
    }
}
