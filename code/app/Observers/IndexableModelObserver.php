<?php
declare(strict_types=1);

namespace App\Observers;

use App\Contracts\Models\CanBeIndexedContract;
use App\Contracts\Repositories\ResourceRepositoryContract;

/**
 * Class IndexableModelObserver
 * @package App\Observers
 */
class IndexableModelObserver
{
    /**
     * @var ResourceRepositoryContract
     */
    private $resourceRepository;

    /**
     * IndexableModelObserver constructor.
     * @param ResourceRepositoryContract $resourceRepository
     */
    public function __construct(ResourceRepositoryContract $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * Handle the CanBeIndexedContract "created" event.
     *
     * @param CanBeIndexedContract $model
     * @return void
     */
    public function created(CanBeIndexedContract $model)
    {
        $this->indexModel($model);
    }

    /**
     * Handle the CanBeIndexedContract "updated" event.
     *
     * @param CanBeIndexedContract $model
     * @return void
     */
    public function updated(CanBeIndexedContract $model)
    {
        $this->indexModel($model);
    }

    /**
     * Creates an index of the model
     *
     * @param CanBeIndexedContract $model
     */
    private function indexModel(CanBeIndexedContract $model)
    {
        if ($model->getContentString()) {
            $data = [
                'content' => $model->getContentString(),
                'resource_id' => $model->id,
                'resource_type' => $model->morphRelationName(),
            ];

            if ($model->resource) {
                $this->resourceRepository->update($model->resource, $data);
            } else {
                $this->resourceRepository->create($data);
            }
        }
    }

    /**
     * Handle the CanBeIndexedContract "deleted" event.
     *
     * @param CanBeIndexedContract $event
     * @return void
     */
    public function deleted(CanBeIndexedContract $event)
    {
        if ($event->resource) {
            $this->resourceRepository->delete($event->resource);
        }
    }
}