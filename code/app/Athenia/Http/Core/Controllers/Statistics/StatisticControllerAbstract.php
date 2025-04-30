<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Requests\Statistics\DeleteRequestAbstract;
use App\Athenia\Http\Core\Requests\Statistics\IndexRequestAbstract;
use App\Athenia\Http\Core\Requests\Statistics\ViewRequestAbstract;
use App\Athenia\Http\Core\Requests\Statistics\StoreRequestAbstract;
use App\Athenia\Http\Core\Requests\Statistics\UpdateRequestAbstract;
use App\Athenia\Models\Statistics\Statistic;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;

/**
 * Class StatisticControllerAbstract
 * @package App\Athenia\Http\Core\Controllers\Statistics
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
     * @param IndexRequestAbstract $request
     * @return JsonResponse
     */
    public function index(IndexRequestAbstract $request): JsonResponse
    {
        return $this->response($this->repository->findAll());
    }

    /**
     * Creates a Statistic model
     *
     * @param StoreRequestAbstract $request
     * @return JsonResponse
     */
    public function store(StoreRequestAbstract $request): JsonResponse
    {
        return $this->response($this->repository->create($request->validated()));
    }

    /**
     * View a single Statistic model
     *
     * @param ViewRequestAbstract $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function show(ViewRequestAbstract $request, Statistic $statistic): JsonResponse
    {
        return $this->response($statistic);
    }

    /**
     * Updates a Statistic model
     *
     * @param UpdateRequestAbstract $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function update(UpdateRequestAbstract $request, Statistic $statistic): JsonResponse
    {
        return $this->response($this->repository->update($statistic, $request->validated()));
    }

    /**
     * Deletes a Statistic model
     *
     * @param DeleteRequestAbstract $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function destroy(DeleteRequestAbstract $request, Statistic $statistic): JsonResponse
    {
        $this->repository->delete($statistic);
        return $this->response(null);
    }
} 