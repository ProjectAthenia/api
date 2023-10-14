<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers;

use App\Contracts\Repositories\CategoryRepositoryContract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class MemberCardControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class CategoryControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var CategoryRepositoryContract
     */
    protected CategoryRepositoryContract $repository;

    /**
     * MemberCardController constructor.
     * @param CategoryRepositoryContract $repository
     */
    public function __construct(CategoryRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Category\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(Requests\Category\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Display the specified resource.
     *
     * @param Requests\Category\ViewRequest $request
     * @param Category $model
     * @return Category
     */
    public function show(Requests\Category\ViewRequest $request, Category $model)
    {
        return $model->load($this->expand($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\Category\StoreRequest $request
     * @return Category
     */
    public function store(Requests\Category\StoreRequest $request)
    {
        $model = $this->repository->create($request->json()->all());
        return response($model, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\Category\UpdateRequest $request
     * @param Category $membershipPlan
     * @return BaseModelAbstract
     */
    public function update(Requests\Category\UpdateRequest $request, Category $membershipPlan)
    {
        return $this->repository->update($membershipPlan, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\Category\DeleteRequest $request
     * @param Category $model
     * @return null
     */
    public function destroy(Requests\Category\DeleteRequest $request, Category $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}
