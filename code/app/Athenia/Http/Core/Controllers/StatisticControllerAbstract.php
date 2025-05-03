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
    public function index(Requests\Statistics\IndexRequest $request)
    {
        return $this->repository->findAll();
    }

    /**
     * Creates a Statistic model
     *
     * @param Requests\Statistics\StoreRequest $request
     * @return JsonResponse
     */
    public function store(Requests\Statistics\StoreRequest $request)
    {
        $model = $this->repository->create($request->validated());
        return response($model, 201);
    }

    /**
     * View a single Statistic model
     *
     * @param Requests\Statistics\ViewRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function show(Requests\Statistics\ViewRequest $request, Statistic $statistic)
    {
        return $statistic;
    }

    /**
     * Updates a Statistic model
     *
     * @param Requests\Statistics\UpdateRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function update(Requests\Statistics\UpdateRequest $request, Statistic $statistic)
    {
        return $this->repository->update($statistic, $request->validated());
    }

    /**
     * Deletes a Statistic model
     *
     * @param Requests\Statistics\DeleteRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function destroy(Requests\Statistics\DeleteRequest $request, Statistic $statistic)
    {
        $this->repository->delete($statistic);
        return response(null, 204);
    }
} 