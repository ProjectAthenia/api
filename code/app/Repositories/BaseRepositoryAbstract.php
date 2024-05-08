<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Models\CanBeMorphedToContract;
use AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface as LogContract;
use App\Contracts\Repositories\BaseRepositoryContract;
use App\Exceptions\NotImplementedException;
use App\Models\BaseModelAbstract;

/**
 * Class BaseRepositoryAbstract
 * @package App\Repositories
 */
abstract class BaseRepositoryAbstract implements BaseRepositoryContract
{
    /**
     * @var BaseModelAbstract
     */
    protected $model;

    /**
     * @var LogContract
     */
    protected $log;

    /**
     * BaseRepositoryAbstract constructor.
     * @param $model
     * @param LogContract $log
     */
    public function __construct($model, LogContract $log)
    {
        $this->model = $model;
        $this->log = $log;
    }
    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param $id
     * @param array [$with] relationships to load eagerly
     *
     * @throws ModelNotFoundException
     *
     * @return BaseModelAbstract
     */
    public function findOrFail($id, array $with = [])
    {
        return $this->model->with($with)->findOrFail($id);
    }

    /**
     * Builds the find all query
     *
     * @param array $where
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param array $belongsToArray
     * @return EloquentJoinBuilder
     */
    protected function buildFindAllQuery(array $where = [], array $searches = [], array $orderBy = [], array $with = [], array $belongsToArray = [])
    {
        /** @var EloquentJoinBuilder $result */
        $result = $this->model->with($with);

        foreach ($belongsToArray as $parentModel) {
            $parentModelPluralFunction = $this->getRelationshipFunctionName($parentModel, $this->model);

            $parentRelationship = $this->model->$parentModelPluralFunction();

            switch (true) {
                case ($parentRelationship instanceof BelongsTo):
                    $queryKey = $parentRelationship->getQualifiedForeignKeyName();
                    $parentModelKeyField = $parentRelationship->getOwnerKeyName();

                    break;

                case ($parentRelationship instanceof BelongsToMany):
                    $queryKey = $parentRelationship->getQualifiedRelatedPivotKeyName();
                    $parentModelKeyField = $parentRelationship->getRelated()->getKeyName();
                    break;

                // @codeCoverageIgnoreStart
                // this is just in case some other relationship gets introduced
                default:
                    throw new NotImplementedException('A relationship has not yet been handled.');
                // @codeCoverageIgnoreEnd
            }

            $parentModelValue = $parentModel->$parentModelKeyField;
            $result->whereHas($parentModelPluralFunction, function($query) use ($queryKey, $parentModelValue) {
                $query->where($queryKey, '=', $parentModelValue);
            });
        }

        foreach ($where as $key => $query) {
            if (is_array($query)) {
                if ($query[1] == 'in') {
                    $result->whereIn($query[0], $query[2]);
                } else if ($query[1] == 'not in') {
                    $result->whereNotIn($query[0], $query[2]);
                } else if ($query[1] == 'IS NULL') {
                    $result->whereJoin($query[0], null, null);
                } else if ($query[1] == 'IS NOT NULL') {
                    $result->whereNotNull($query[0]);
                } else {
                    $result->whereJoin(...$query);
                }
            } else {
                $result->whereJoin($key, '=', $query);
            }
        }

        if (count($searches)) {

            $result->where(function (EloquentJoinBuilder $query) use ($searches) {
                foreach ($searches as $key => $where) {
                    if (is_array($where)) {
                        if ($where[1] == 'in') {
                            $query->orWhereIn($where[0], $where[2]);
                        } else if ($where[1] == 'not in') {
                            $query->orWhereNotIn($where[0], $where[2]);
                        } else if ($where[1] == 'IS NULL') {
                            $query->orWhereNull($where[0]);
                        } else if ($where[1] == 'IS NOT NULL') {
                            $query->orWhereNotNull($where[0]);
                        } else {
                            $query->orWhereJoin(...$where);
                        }
                    } else {
                        $query->orWhereJoin($key, '=', $where);
                    }
                }
            });
        }

        foreach ($orderBy as $field => $direction) {
            $result->orderBy($field, $direction);
        }

        return $result;
    }

    /**
     * Finishes a find all query for you. All custom queries should be sandwiched between the above build function and a return of this.
     *
     * @param EloquentJoinBuilder $query
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator|Collection
     */
    protected function finalizeFindAllQuery(EloquentJoinBuilder $query, $limit = 10, int $page = 1)
    {
        if ($limit) {
            return $query->paginate($limit, ['*'], 'page', $page);
        }

        return $query->get();
    }

    /**
     * Find all
     *
     * @param array $filters
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param int|null $limit pass null to get all
     * @param array $belongsToArray array of models this should belong to
     * @param int $page
     * @return LengthAwarePaginator|Collection
     */
    public function findAll(array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $page = 1)
    {
        $query = $this->buildFindAllQuery($filters, $searches, $orderBy, $with, $belongsToArray);

        return $this->finalizeFindAllQuery($query, $limit, $page);
    }

    /**
     * Save a new instance of this model, and then return the instance
     *
     * In cases where we want to force the data, pass in forcedvalues - this is rare.
     *
     * @param array $data
     * @param BaseModelAbstract $relatedModel if there is a parent to assign this to
     * @param array $forcedValues
     * @return BaseModelAbstract
     * @throws NotImplementedException
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $newModel = $this->model->newInstance($data);

        foreach ($forcedValues as $key => $value) {
            $newModel->{$key} = $value;
        }

        if ($relatedModel) {
            $relatedModelPluralFunction = $this->getRelationshipFunctionName($relatedModel, $this->model);

            $relationship = $this->model->$relatedModelPluralFunction();

            switch (true) {
                case ($relationship instanceof BelongsTo):
                    $parentKey = $relationship->getForeignKeyName();
                    $parentIdKey = $relationship->getOwnerKeyName();
                    $newModel->$parentKey = $relatedModel->$parentIdKey;
                    break;

                case ($relationship instanceof BelongsToMany):
                    $newModel->save(); // need to do this to get the ID

                    $newModel->$relatedModelPluralFunction()->attach($relatedModel->{$relatedModel->getKeyName()});
                    break;

                case ($relationship instanceof HasOne):
                case ($relationship instanceof HasMany):
                    $newModel->save();

                    $ownerKey = $relationship->getForeignKeyName();
                    $localKey = explode('.', $relationship->getQualifiedParentKeyName())[1];

                    $relatedModel->$ownerKey = $newModel->$localKey;
                    $relatedModel->save();
                    break;

                // @codeCoverageIgnoreStart
                // this is just in case some other relationship gets introduced
                default:
                    throw new NotImplementedException('A relationship has not yet been handled.');
                // @codeCoverageIgnoreEnd
            }
        }

        if (!$newModel->wasRecentlyCreated) {
            $newModel->save(); // because one of the parent model relationships saves it first.
        }

        $this->log->info('Created model', ['model_id'=>$newModel->id, 'model' => get_class($newModel)]);
        return $newModel;
    }

    /**
     * Update the model
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        if ($forcedValues) {
            $model->forceFill($forcedValues);
        }
        if (!$model->update($data)) {
            throw new \DomainException(sprintf('%s[%d] failed to update', get_class($model), $model->id));
        }
        $this->log->info('Updated model', ['model_id' => $model->id, 'model' => get_class($model)]);
        return $model;
    }

    /**
     * Syncs all child data with full models
     *
     * @param BaseRepositoryContract $childRepository
     * @param BaseModelAbstract|CanBeMorphedToContract $parentModel
     * @param array $childrenData
     * @param Collection|null $existingChildren
     * @param string|null $morphRelationship
     */
    protected function syncChildModels(BaseRepositoryContract $childRepository, BaseModelAbstract $parentModel,
                                       array $childrenData, Collection $existingChildren = null,
                                       string $morphRelationship = null)
    {
        if ($existingChildren) {
            $newChildrenIds = collect($childrenData)->pluck('id');

            foreach ($existingChildren as $child) {
                if (!$newChildrenIds->contains($child->id)) {
                    $childRepository->delete($child);
                }
            }
        }

        foreach ($childrenData as $childrenDatum) {
            $id = $childrenDatum['id'] ?? null;
            /** @var BaseModelAbstract|null $existingModel */
            $existingModel = $id && $existingChildren ? $existingChildren->firstWhere('id', $id) : null;

            if ($existingModel) {
                $childRepository->update($existingModel, $childrenDatum);
            } else {
                if ($morphRelationship) {
                    $childrenDatum[$morphRelationship . '_id'] = $parentModel->id;
                    $childrenDatum[$morphRelationship . '_type'] = $parentModel->morphRelationName();
                    $childRepository->create($childrenDatum);
                } else {
                    $childRepository->create($childrenDatum, $parentModel);
                }
            }
        }
    }

    /**
     * Delete this single model
     *
     * @param BaseModelAbstract $model
     * @return bool|null
     * @throws \Exception
     */
    public function delete(BaseModelAbstract $model)
    {
        if (!$model->delete()) {
            throw new \DomainException(sprintf('%s[%d] failed to delete', get_class($model), $model->id));
        }
        $this->log->info('Deleted model', ['model_id' => $model->id, 'model' => get_class($model)]);
        return true;
    }

    /**
     * Try to build the relationship model function name
     *
     * @param BaseModelAbstract $model
     * @param BaseModelAbstract $parentModel
     * @return string
     */
    protected function getRelationshipFunctionName(BaseModelAbstract $model, BaseModelAbstract $parentModel): string
    {
        $method = Str::camel(Str::plural(class_basename($model)));
        if (!method_exists($parentModel, $method)) {
            $method = Str::camel(class_basename($model));
        }
        return $method;
    }
}
