<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Exceptions\NotImplementedException;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class RoleRepositoryTest
 * @package Tests\Integration\Repositories
 */
class RoleRepositoryTest extends TestCase
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

    public function testFindAllSuccess()
    {
        $items = $this->repository->findAll([], [], [], [], 0);
        $this->assertCount(Role::count(), $items);
    }

    public function testUpdate()
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->update(new Role(), []);
    }

    public function testDelete()
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->delete(new Role());
    }

    public function testFindOrFail()
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreate()
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->create([]);
    }
}
