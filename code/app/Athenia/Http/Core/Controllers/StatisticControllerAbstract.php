<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Statistic\StatisticRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Statistic\Statistic;
use Illuminate\Http\JsonResponse;

/**
 * Class StatisticControllerAbstract
 * @package App\Athenia\Http\Core\Controllers
 */
abstract class StatisticControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

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
     * @param Requests\Statistic\IndexRequest $request
     * @return JsonResponse
     */
    public function index(Requests\Statistic\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Creates a Statistic model
     *
     * @param Requests\Statistic\StoreRequest $request
     * @return JsonResponse
     */
    public function store(Requests\Statistic\StoreRequest $request)
    {
        $model = $this->repository->create($request->json()->all());
        return response($model, 201);
    }

    /**
     * View a single Statistic model
     *
     * @param Requests\Statistic\ViewRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function show(Requests\Statistic\ViewRequest $request, Statistic $statistic)
    {
        return $statistic->load($this->expand($request));
    }

    /**
     * Updates a Statistic model
     *
     * @param Requests\Statistic\UpdateRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function update(Requests\Statistic\UpdateRequest $request, Statistic $statistic)
    {
        return $this->repository->update($statistic, $request->json()->all());
    }

    /**
     * Deletes a Statistic model
     *
     * @param Requests\Statistic\DeleteRequest $request
     * @param Statistic $statistic
     * @return JsonResponse
     */
    public function destroy(Requests\Statistic\DeleteRequest $request, Statistic $statistic)
    {
        $this->repository->delete($statistic);
        return response(null, 204);
    }
} 