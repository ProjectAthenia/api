<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Collection\CollectionRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasViewRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\Category;
use App\Models\Collection\Collection;

abstract class CollectionControllerAbstract
{
    use HasViewRequests;

    /**
     * @param CollectionRepositoryContract $repository
     */
    public function __construct(protected CollectionRepositoryContract $repository)
    {}

    /**
     * Display the specified resource.
     *
     * @param \App\Athenia\Http\Core\Requests\Collection\ViewRequest $request
     * @param Collection $model
     * @return Category
     */
    public function show(\App\Athenia\Http\Core\Requests\Collection\ViewRequest $request, Collection $model)
    {
        return $model->load($this->expand($request));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Athenia\Http\Core\Requests\Collection\UpdateRequest $request
     * @param Collection $model
     * @return BaseModelAbstract
     */
    public function update(\App\Athenia\Http\Core\Requests\Collection\UpdateRequest $request, Collection $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Athenia\Http\Core\Requests\Collection\DeleteRequest $request
     * @param Collection $model
     * @return null
     */
    public function destroy(\App\Athenia\Http\Core\Requests\Collection\DeleteRequest $request, Collection $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}