<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\User;

use App\Athenia\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class BallotCompletionControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class BallotCompletionControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var BallotCompletionRepositoryContract
     */
    private BallotCompletionRepositoryContract $repository;

    /**
     * BallotCompletionControllerAbstract constructor.
     * @param BallotCompletionRepositoryContract $repository
     */
    public function __construct(BallotCompletionRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\User\BallotCompletion\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\BallotCompletion\IndexRequest $request, User $user)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$user], (int)$request->input('page', 1));
    }
}
