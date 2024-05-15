<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface BaseRepositoryContract
 * @package App\Contracts\Repositories
 */
interface BaseRepositoryContract
{
    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param $id
     * @param array [$with] relationships to load eagerly
     *
     * @return BaseModelAbstract
     */
    public function findOrFail($id, array $with = []);

    /**
     * Find all
     *
     * @param array $filters
     * @param array $searches
     * @param array $orderBy This needs to be an array of of field names with each value indicating the needed order
     * @param array $with
     * @param int $limit
     * @param array $belongsToArray array of models this should belong to
     * @param int|null $page pass in null to get all
     * @return LengthAwarePaginator|Collection
     */
    public function findAll(array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $page = 1);

    /**
     * Save a new instance of this model, and then return the instance
     *
     * @param array $data
     * @param BaseModelAbstract $relatedModel if there is a relationship to build
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = []);

    /**
     * Update the model
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract;

    /**
     * Delete this single model
     *
     * @param BaseModelAbstract $model
     * @return bool|null
     */
    public function delete(BaseModelAbstract $model);
}