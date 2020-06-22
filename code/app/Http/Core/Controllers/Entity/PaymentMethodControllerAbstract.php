<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Entity;

use App\Contracts\Models\IsAnEntity;
use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Services\StripeCustomerServiceContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;

/**
 * Class PaymentMethodControllerAbstract
 * @package App\Http\Core\Controllers\Entity
 */
abstract class PaymentMethodControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $repository;

    /**
     * @var StripeCustomerServiceContract
     */
    private $stripeCustomerService;

    /**
     * PaymentMethodController constructor.
     * @param PaymentMethodRepositoryContract $repository
     * @param StripeCustomerServiceContract $stripeCustomerService
     */
    public function __construct(PaymentMethodRepositoryContract $repository,
                                StripeCustomerServiceContract $stripeCustomerService)
    {
        $this->repository = $repository;
        $this->stripeCustomerService = $stripeCustomerService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @SWG\Post(
     *     path="/users/{user_id}/payment-methods",
     *     summary="Create a new Payment Method model",
     *     tags={"PaymentMethods","Users"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/PaymentMethod"),
     *          description="The model to create"
     *     ),
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the user model"
     *     ),
     *     @SWG\Response(
     *          response=201,
     *          description="Model created successfully",
     *          @SWG\Schema(ref="#/definitions/PaymentMethod"),
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
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\Entity\PaymentMethod\StoreRequest $request
     * @param IsAnEntity $entity
     * @return JsonResponse
     */
    public function store(Requests\Entity\PaymentMethod\StoreRequest $request, IsAnEntity $entity)
    {
        $data = $request->json()->all();

        $model = $this->stripeCustomerService->createPaymentMethod($entity, $data['token']);
        return new JsonResponse($model, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @SWG\Delete(
     *     path="/users/{user_id}/payment-methods/{id}",
     *     summary="Delete a single payment method",
     *     tags={"Characters"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the user model"
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the model"
     *     ),
     *     @SWG\Response(
     *          response=204,
     *          description="Successful deletion",
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
     * @param Requests\Entity\PaymentMethod\DeleteRequest $request
     * @param IsAnEntity $entity
     * @param PaymentMethod $paymentMethod
     * @return null
     */
    public function destroy(Requests\Entity\PaymentMethod\DeleteRequest $request, IsAnEntity $entity, PaymentMethod $paymentMethod)
    {
        $this->repository->delete($paymentMethod);
        return response(null, 204);
    }
}