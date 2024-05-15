<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\RoleRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class RoleControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class RoleControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var RoleRepositoryContract
     */
    protected $roleRepository;

    /**
     * RoleController constructor.
     * @param RoleRepositoryContract $roleRepository
     */
    public function __construct(RoleRepositoryContract $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @SWG\Get(
     *     path="/roles",
     *     summary="Get all roles",
     *     tags={"Roles"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(ref="#/parameters/PaginationPage"),
     *     @SWG\Parameter(ref="#/parameters/PaginationLimit"),
     *     @SWG\Parameter(ref="#/parameters/SearchParameter"),
     *     @SWG\Parameter(ref="#/parameters/FilterParameter"),
     *     @SWG\Parameter(ref="#/parameters/ExpandParameter"),
     *     @SWG\Response(
     *          response=200,
     *          description="Returns a collection of the model",
     *          @SWG\Schema(ref="#/definitions/PagedRoles"),
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
     *     definition="PagedRoles",
     *     allOf={
     *          @SWG\Schema(ref="#/definitions/Roles"),
     *          @SWG\Schema(ref="#/definitions/Paging")
     *     }
     * )
     * @SWG\Definition(
     *     definition="Roles",
     *     @SWG\Property(
     *          property="data",
     *          type="array",
     *          minItems=0,
     *          maxItems=100,
     *          uniqueItems=true,
     *          @SWG\Items(ref="#/definitions/Role")
     *     )
     * )
     *
     * @param \App\Athenia\Http\Core\Requests\Role\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(\App\Athenia\Http\Core\Requests\Role\IndexRequest $request)
    {
        return $this->roleRepository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}