<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Vote\BallotRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasViewRequests;
use App\Http\Core\Requests;
use App\Models\Vote\Ballot;

/**
 * Class BallotControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class BallotControllerAbstract extends BaseControllerAbstract
{
    use HasViewRequests;

    /**
     * @var BallotRepositoryContract
     */
    protected BallotRepositoryContract $repository;

    /**
     * BallotControllerAbstract constructor.
     * @param BallotRepositoryContract $repository
     */
    public function __construct(BallotRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Ballot\ViewRequest $request
     * @param Ballot $ballot
     * @return Ballot
     */
    public function show(Requests\Ballot\ViewRequest $request, Ballot $ballot)
    {
        return $ballot->load($this->expand($request));
    }
}
