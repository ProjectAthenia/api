<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Requests\Statistics\DeleteRequest;
use App\Athenia\Http\Core\Requests\Statistics\IndexRequest;
use App\Athenia\Http\Core\Requests\Statistics\ViewRequest;
use App\Athenia\Http\Core\Requests\Statistics\StoreRequest;
use App\Athenia\Http\Core\Requests\Statistics\UpdateRequest;
use App\Athenia\Models\Statistics\Statistic;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;

/**
 * Class StatisticControllerAbstract
 */
abstract class StatisticControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var StatisticRepositoryContract
     */
    protected $repository;

    /**
     * StatisticControllerAbstract constructor.
     * @param Gate $gate
     * @param StatisticRepositoryContract $repository
     */
    public function __construct(Gate $gate, StatisticRepositoryContract $repository)
    {
        parent::__construct($gate);
        $this->repository = $repository;
    }

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