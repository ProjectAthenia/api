<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Entity;

use App\Contracts\Models\IsAnEntityContract;
use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PaymentControllerAbstract
 * @package App\Http\Core\Controllers\Entity
 */
abstract class PaymentControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var PaymentRepositoryContract
     */
    private PaymentRepositoryContract $repository;

    /**
     * PaymentControllerAbstract constructor.
     * @param PaymentRepositoryContract $repository
     */
    public function __construct(PaymentRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Entity\Payment\IndexRequest $request
     * @param IsAnEntityContract $entity
     * @return LengthAwarePaginator|Collection
     */
    public function index(Requests\Entity\Payment\IndexRequest $request, IsAnEntityContract $entity)
    {
        $filter = $this->filter($request);

        $filter[] = [
            'owner_id',
            '=',
            $entity->id,
        ];
        $filter[] = [
            'owner_type',
            '=',
            $entity->morphRelationName(),
        ];

        return $this->repository->findAll($filter, $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}