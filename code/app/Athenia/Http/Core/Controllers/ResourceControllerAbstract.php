<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;

/**
 * Class ResourceControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class ResourceControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ResourceRepositoryContract
     */
    private $repository;

    /**
     * ResourcesController constructor.
     * @param ResourceRepositoryContract $repository
     */
    public function __construct(ResourceRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Resource\IndexRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Requests\Resource\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}