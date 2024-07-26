<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Indexing;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;

interface ResourceRepositoryServiceContract
{
    /**
     * Gets all resource repositories used in our app
     *
     * @return array<BaseRepositoryContract>
     */
    public function getResourceRepositories(): array;
}