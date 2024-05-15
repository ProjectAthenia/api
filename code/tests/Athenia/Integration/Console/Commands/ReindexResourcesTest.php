<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Console\Commands;

use App\Athenia\Console\Commands\ReindexResources;
use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Repositories\ResourceRepository;
use App\Athenia\Repositories\User\UserRepository;
use App\Models\Resource;
use App\Models\User\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\MocksConsoleOutput;

/**
 * Class ReindexResourcesTest
 * @package Tests\Athenia\Integration\Console\Commands
 */
final class ReindexResourcesTest extends TestCase
{
    use MocksApplicationLog, MocksConsoleOutput, DatabaseSetupTrait;

    /**
     * @var ReindexResources
     */
    private $command;

    /**
     * @var ResourceRepositoryContract
     */
    private $resourceRepository;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->resourceRepository = new ResourceRepository(new Resource(), $this->getGenericLogMock());
        $this->userRepository = new UserRepository(
            new User(),
            $this->getGenericLogMock(),
            mock(Hasher::class),
            mock(Repository::class),
        );

        $this->command = new ReindexResources(
            $this->resourceRepository,
            $this->userRepository,
        );
        $this->mockConsoleOutput($this->command);
    }

    public function testIndexUsers(): void
    {
        User::unsetEventDispatcher();

        User::factory()->create();

        Resource::factory()->count( 3)->create();

        $this->assertCount(3, Resource::all());
        $this->assertCount(4, User::all());

        $this->command->indexData($this->userRepository);

        $this->assertCount(4, Resource::all());
    }
}
