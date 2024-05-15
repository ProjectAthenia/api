<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface HasLocationDataRepositoryContract
 * @package App\Contracts\Repositories
 */
interface HasLocationRepositoryContract
{
    /**
     * Finds all requests around a specific location
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius in KM
     * @param array $filters
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param int $limit
     * @param array $belongsToArray
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAllAroundLocation(float $latitude, float $longitude, float $radius, array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $page = 1): LengthAwarePaginator;
}