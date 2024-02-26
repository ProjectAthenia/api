<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Collection;

use App\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Collection\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ThreadControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class CollectionItemControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * ThreadController constructor.
     * @param CollectionItemRepositoryContract $repository
     */
    public function __construct(protected CollectionItemRepositoryContract $repository)
    {}

    /**
     * @param Requests\Collection\CollectionItem\IndexRequest $request
     * @param Collection $collection
     * @return LengthAwarePaginator
     */
    public function index(Requests\Collection\CollectionItem\IndexRequest $request, Collection $collection)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$collection], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\Collection\CollectionItem\StoreRequest $request
     * @param Collection $collection
     * @return JsonResponse
     */
    public function store(Requests\Collection\CollectionItem\StoreRequest $request, Collection $collection) : JsonResponse
    {
        $data = $request->json()->all();
        return new JsonResponse($this->repository->create($data, $collection), 201);
    }
}