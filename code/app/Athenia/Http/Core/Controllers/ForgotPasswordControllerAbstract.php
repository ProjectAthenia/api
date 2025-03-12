<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Http\Core\Requests;
use Illuminate\Http\JsonResponse;

/**
 * Class ForgotPasswordControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class ForgotPasswordControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var PasswordTokenRepositoryContract
     */
    private $passwordTokenRepository;

    /**
     * ForgotPasswordController constructor.
     * @param UserRepositoryContract $userRepository
     * @param PasswordTokenRepositoryContract $passwordTokenRepository
     */
    public function __construct(UserRepositoryContract $userRepository,
                                PasswordTokenRepositoryContract $passwordTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->passwordTokenRepository = $passwordTokenRepository;
    }

    /**
     * Generates a forgot password token for a user, and sends a status model
     *
     * @SWG\Post(
     *     path="/forgot-password",
     *     summary="Login to get JWT, should receive email and password.",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"ForgotPassword"},
     *     @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          description="The user's email",
     *          type="string",
     *          format="email"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Successful generated a password token",
     *          @SWG\Schema(ref="#/definitions/Status"),
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
     * @param Requests\ForgotPassword\ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(Requests\ForgotPassword\ForgotPasswordRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->input('email'));

        $token = $this->passwordTokenRepository->generateUniqueToken($user);

        $this->passwordTokenRepository->create(['token' => $token], $user);

        return new JsonResponse([
            'status' => 'OK',
        ]);
    }

    /**
     * Resets a user's password
     *
     * @SWG\Post(
     *     path="/reset-password",
     *     summary="Login to get JWT, should receive email and password.",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"ForgotPassword"},
     *     @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          description="The user's email",
     *          type="string",
     *          format="email",
     *     ),
     *     @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          description="The new password",
     *          type="string",
     *     ),
     *     @SWG\Parameter(
     *          name="token",
     *          in="formData",
     *          description="The token that was sent to the user's email address",
     *          type="string",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Successful changed the user's password",
     *          @SWG\Schema(ref="#/definitions/Status"),
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
     * @param Requests\ForgotPassword\ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(Requests\ForgotPassword\ResetPasswordRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->input('email'));

        $password = $request->input('password');

        $this->userRepository->update($user, ['password' => $password]);

        return new JsonResponse([
            'status' => 'OK',
        ]);
    }
}