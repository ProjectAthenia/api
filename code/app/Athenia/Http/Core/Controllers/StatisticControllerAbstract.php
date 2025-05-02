<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Http\Core\Requests;
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
     * StatisticControllerAbstract constructor.
     * @param StatisticRepositoryContract $repository
     */
    public function __construct(StatisticRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource
     *
     * @param Requests\Statistics\IndexRequest $request
     * @return JsonResponse
     */
    public function index(Requests\Statistics\IndexRequest $request): JsonResponse
    {
        return $this->response($this->repository->findAll());
    }

    /**
     * Creates a Statistic model
     *
     * @param Requests\Statistics\StoreRequest $request
     * @return JsonResponse
     */
    public function store(Requests\Statistics\StoreRequest $request): JsonResponse
    {
        return $this->response($this->repository->create($request->validated()), 201);
    }

    /**
     * View a single Statistic model
     *
     * @param Requests\Statistics\ViewRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function show(Requests\Statistics\ViewRequest $request, Statistic $statistic): JsonResponse
    {
        return $this->response($statistic);
    }

    /**
     * Updates a Statistic model
     *
     * @param Requests\Statistics\UpdateRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function update(Requests\Statistics\UpdateRequest $request, Statistic $statistic): JsonResponse
    {
        return $this->response($this->repository->update($statistic, $request->validated()));
    }

    /**
     * Deletes a Statistic model
     *
     * @param Requests\Statistics\DeleteRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function destroy(Requests\Statistics\DeleteRequest $request, Statistic $statistic): JsonResponse
    {
        $this->repository->delete($statistic);
        return $this->response(null, 204);
    }
} 