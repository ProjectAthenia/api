<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Ballot;

use App\Athenia\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Requests;
use App\Models\Vote\Ballot;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class BallotCompletionControllerAbstract
 * @package App\Http\Core\Controllers\Ballot
 */
abstract class BallotCompletionControllerAbstract extends BaseControllerAbstract
{
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
     * @param \App\Athenia\Http\Core\Requests\Ballot\BallotCompletion\StoreRequest $request
     * @param Ballot $ballot
     * @return JsonResponse
     */
    public function store(\App\Athenia\Http\Core\Requests\Ballot\BallotCompletion\StoreRequest $request, Ballot $ballot)
    {
        $data = $request->json()->all();

        $data['user_id'] = Auth::user()->id;

        $model = $this->repository->create($data, $ballot);
        $model->load('votes');

        return new JsonResponse($model, 201);
    }
}
