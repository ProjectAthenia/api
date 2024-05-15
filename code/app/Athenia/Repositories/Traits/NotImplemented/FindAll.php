<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Traits\NotImplemented;

use App\Athenia\Exceptions\NotImplementedException;

/**
 * Class FindAll
 * @package App\Repositories\Traits\NotImplemented
 */
trait FindAll
{
    /**
     * Not Implemented
     *
     * @param array $filters
     * @param array $searches
     * @param array $with
     * @param int $limit
     * @param array $belongsToArray array of models this should belong to
     * @param int|null $page pass in null to get all
     * @param array $orderBy
     * @return void
     */
    public function findAll(array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $page = 1)
    {
        throw new NotImplementedException();
    }
}