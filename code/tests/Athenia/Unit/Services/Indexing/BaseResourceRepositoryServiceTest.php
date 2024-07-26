<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Indexing;

use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Repositories\User\UserRepository;
use App\Athenia\Services\Indexing\BaseResourceRepositoryService;
use Illuminate\Contracts\Foundation\Application;
use Tests\TestCase;

class BaseResourceRepositoryServiceTest extends TestCase
{
    public function testGetResourceRepositories()
    {
        $app = mock(Application::class);

        $userRepository = mock(UserRepository::class);

        $app->shouldReceive('make')->with(UserRepositoryContract::class)->andReturn($userRepository);

        $resourceRepositoryService = new class($app) extends BaseResourceRepositoryService {

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

        $result = $resourceRepositoryService->getResourceRepositories();

        $this->assertEquals([$userRepository], $result);
    }
}