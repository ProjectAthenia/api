<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Console\Commands;

use App\Athenia\Console\Commands\ReindexResources;
use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Repositories\ResourceRepository;
use App\Athenia\Repositories\User\UserRepository;
use App\Athenia\Services\Indexing\BaseResourceRepositoryService;
use App\Models\Resource;
use App\Models\User\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
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
    private ResourceRepositoryContract $resourceRepository;

    /**
     * @var BaseResourceRepositoryService
     */
    private BaseResourceRepositoryService $resourceRepositoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->resourceRepository = new ResourceRepository(new Resource(), $this->getGenericLogMock());

        $app = mock(Application::class);

        $app->shouldReceive('make')->with(UserRepositoryContract::class)->andReturn(
            new UserRepository(
                new User(),
                $this->getGenericLogMock(),
                mock(Hasher::class),
                mock(Repository::class),
            )
        );

        $this->resourceRepositoryService = new class($app) extends BaseResourceRepositoryService {

            /**
             * All repo interfaces for enabled resources in this app
             *
             * @return array<class-string>
             */
            public function availableResourceRepositories(): array
            {
                return [
                    UserRepositoryContract::class
                ];
            }
        };

        $this->command = new ReindexResources(
            $this->resourceRepository,
            $this->resourceRepositoryService,
        );
        $this->mockConsoleOutput($this->command);
    }

    public function testHandle(): void
    {
        User::unsetEventDispatcher();

        User::factory()->create();

        Resource::factory()->count( 3)->create();

        $this->assertCount(3, Resource::all());
        $this->assertCount(4, User::all());

        $this->command->handle();

        $this->assertCount(4, Resource::all());
    }
}
