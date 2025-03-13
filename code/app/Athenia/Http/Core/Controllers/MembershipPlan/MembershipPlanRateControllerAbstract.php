<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\MembershipPlan;

use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Subscription\MembershipPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MembershipPlanRateControllerAbstract
 * @package App\Http\Core\Controllers\MembershipPlan
 */
abstract class MembershipPlanRateControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var MembershipPlanRateRepositoryContract
     */
    protected MembershipPlanRateRepositoryContract $repository;

    /**
     * MembershipPlanRateController constructor.
     * @param MembershipPlanRateRepositoryContract $repository
     */
    public function __construct(MembershipPlanRateRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\MembershipPlan\MembershipPlanRate\IndexRequest $request
     * @param MembershipPlan $membershipPlan
     * @return LengthAwarePaginator|Collection
     */
    public function index(Requests\MembershipPlan\MembershipPlanRate\IndexRequest $request, MembershipPlan $membershipPlan)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$membershipPlan], (int)$request->input('page', 1));
    }
}