<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers;

use App\Contracts\Repositories\FeatureRepositoryContract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Feature;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class FeatureControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class FeatureControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var FeatureRepositoryContract
     */
    protected FeatureRepositoryContract $repository;

    /**
     * FeatureControllerAbstract constructor.
     * @param FeatureRepositoryContract $repository
     */
    public function __construct(FeatureRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Feature\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(Requests\Feature\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Display the specified resource.
     *
     * @param Requests\Feature\ViewRequest $request
     * @param Feature $model
     * @return Feature
     */
    public function show(Requests\Feature\ViewRequest $request, Feature $model)
    {
        return $model->load($this->expand($request));
    }
}
