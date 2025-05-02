<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Http\Core\Requests\Statistics\DeleteRequest;
use App\Http\Core\Requests\Statistics\IndexRequest;
use App\Http\Core\Requests\Statistics\ViewRequest;
use App\Http\Core\Requests\Statistics\StoreRequest;
use App\Http\Core\Requests\Statistics\UpdateRequest;
use App\Models\Statistics\Statistic;
use Illuminate\Http\JsonResponse;

/**
 * Class StatisticControllerAbstract
 * @package App\Athenia\Http\Core\Controllers
 */
abstract class StatisticControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var StatisticRepositoryContract
     */
    protected $repository;

    /**
     * Display a listing of the resource
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        return $this->response($this->repository->findAll());
    }

    /**
     * Creates a Statistic model
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->response($this->repository->create($request->validated()));
    }

    /**
     * View a single Statistic model
     *
     * @param ViewRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function show(ViewRequest $request, Statistic $statistic): JsonResponse
    {
        return $this->response($statistic);
    }

    /**
     * Updates a Statistic model
     *
     * @param UpdateRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Statistic $statistic): JsonResponse
    {
        return $this->response($this->repository->update($statistic, $request->validated()));
    }

    /**
     * Deletes a Statistic model
     *
     * @param DeleteRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function destroy(DeleteRequest $request, Statistic $statistic): JsonResponse
    {
        $this->repository->delete($statistic);
        return $this->response(null);
    }
} 