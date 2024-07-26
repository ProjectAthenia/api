<?php
declare(strict_types=1);

namespace App\Athenia\Services\Indexing;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Athenia\Contracts\Services\Indexing\ResourceRepositoryServiceContract;
use Illuminate\Contracts\Foundation\Application;

abstract class BaseResourceRepositoryService implements ResourceRepositoryServiceContract
{
    /**
     * @param Application $app
     */
    public function __construct(private Application $app) {}

    /**
     * All repo interfaces for enabled resources in this app
     *
     * @return array<class-string>
     */
    public abstract function availableResourceRepositories(): array;

    /**
     * Gets all resource repositories used in our app
     *
     * @return array<BaseRepositoryContract>
     */
    public function getResourceRepositories(): array
    {
        return array_map(
            fn (string $interfaceName) => $this->app->make($interfaceName),
            $this->availableResourceRepositories(),
        );
    }
}