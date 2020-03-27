<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\Article;

use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\Wiki\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class IterationController
 * @package App\Http\V1\Controllers
 */
class IterationController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var IterationRepositoryContract
     */
    private $repository;

    /**
     * IterationController constructor.
     * @param IterationRepositoryContract $repository
     */
    public function __construct(IterationRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource
     *
     * @SWG\Get(
     *     path="/articles/{article_id}/iterations",
     *     summary="Get all iterations for an article",
     *     tags={"Articles", "Iterations"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="article_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the article model"
     *     ),
     *     @SWG\Parameter(ref="#/parameters/PaginationPage"),
     *     @SWG\Parameter(ref="#/parameters/PaginationLimit"),
     *     @SWG\Parameter(ref="#/parameters/SearchParameter"),
     *     @SWG\Parameter(ref="#/parameters/FilterParameter"),
     *     @SWG\Parameter(ref="#/parameters/ExpandParameter"),
     *     @SWG\Response(
     *          response=200,
     *          description="Returns a collection of the model",
     *          @SWG\Schema(ref="#/definitions/PagedArticles"),
     *          @SWG\Header(
     *              header="X-RateLimit-Limit",
     *              description="The number of allowed requests in the period",
     *              type="integer"
     *          ),
     *          @SWG\Header(
     *              header="X-RateLimit-Remaining",
     *              description="The number of remaining requests in the period",
     *              type="integer"
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          ref="#/responses/Standard400BadRequestResponse"
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          ref="#/responses/Standard401UnauthorizedResponse"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          ref="#/responses/Standard404PagingRequestTooLarge"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     * @SWG\Definition(
     *     definition="PagedIterations",
     *     allOf={
     *          @SWG\Schema(ref="#/definitions/Iterations"),
     *          @SWG\Schema(ref="#/definitions/Paging")
     *     }
     * )
     *
     * @param Requests\Article\Iteration\IndexRequest $request
     * @param Article $article
     * @return LengthAwarePaginator
     */
    public function index(Requests\Article\Iteration\IndexRequest $request, Article $article)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$article], (int)$request->input('page', 1));
    }
}