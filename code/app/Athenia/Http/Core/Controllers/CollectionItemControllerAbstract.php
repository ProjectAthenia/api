<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasViewRequests;
use App\Http\Core\Requests;
use App\Models\Category;
use App\Models\Collection\CollectionItem;

abstract class CollectionItemControllerAbstract
{
    use HasViewRequests;

    /**
     * @param CollectionItemRepositoryContract $repository
     */
    public function __construct(protected CollectionItemRepositoryContract $repository)
    {}

    /**
     * Display the specified resource.
     *
     * @param Requests\CollectionItem\ViewRequest $request
     * @param CollectionItem $model
     * @return Category
     */
    public function show(Requests\CollectionItem\ViewRequest $request, CollectionItem $model)
    {
        return $model->load($this->expand($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\CollectionItem\DeleteRequest $request
     * @param CollectionItem $model
     * @return null
     */
    public function destroy(Requests\CollectionItem\DeleteRequest $request, CollectionItem $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}