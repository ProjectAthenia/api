<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Contracts\Services\StripeCustomerServiceContract;
use App\Athenia\Http\Core\Controllers\Traits\HasViewRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;

/**
 * Class UserControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class UserControllerAbstract extends BaseControllerAbstract
{
    use HasViewRequests;

    /**
     * @var UserRepositoryContract
     */
    protected $repository;

    /**
     * @var StripeCustomerServiceContract
     */
    protected $stripeCustomerService;

    /**
     * UsersController constructor.
     * @param UserRepositoryContract $repository
     * @param StripeCustomerServiceContract $stripeCustomerService
     */
    public function __construct(UserRepositoryContract $repository,
                                StripeCustomerServiceContract $stripeCustomerService)
    {
        $this->repository = $repository;
        $this->stripeCustomerService = $stripeCustomerService;
    }

    /**
     * Display the specified resource.
     *
     * @SWG\Get(
     *     path="/users/{id}",
     *     summary="Get a single user",
     *     tags={"Users"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the model"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returns a single model",
     *          @SWG\Schema(ref="#/definitions/User"),
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
     *          ref="#/responses/Standard404ItemNotFoundResponse"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\User\ViewRequest $request
     * @param User $user
     * @return User
     */
    public function show(Requests\User\ViewRequest $request, User $user)
    {
        return $user->load($this->expand($request));
    }

    /**
     * Update the specified resource in storage.
     *
     * @SWG\Patch(
     *     path="/users/{id}",
     *     summary="Updates a single user",
     *     tags={"Users"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the model"
     *     ),
     *     @SWG\Parameter(
     *          name="model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/User"),
     *          description="The model updates to make"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Successful update",
     *          @SWG\Schema(ref="#/definitions/User"),
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
     *          ref="#/responses/Standard404ItemNotFoundResponse"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\User\UpdateRequest $request
     * @param User $user
     * @return User|BaseModelAbstract
     */
    public function update(Requests\User\UpdateRequest $request, User $user)
    {
        return $this->repository->update($user, $request->json()->all());
    }

    /**
     * Display the specified resource's self
     *
     * @SWG\Get(
     *     path="/users/me",
     *     summary="Show currently logged in user info",
     *     tags={"Users"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Response(
     *          response=200,
     *          description="Returns a single model",
     *          @SWG\Schema(ref="#/definitions/User"),
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
     *          ref="#/responses/Standard404ItemNotFoundResponse"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\User\MeRequest $request
     * @return JsonResponse
     */
    public function me(Requests\User\MeRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        return new JsonResponse($user->load($this->expand($request)));
    }
}