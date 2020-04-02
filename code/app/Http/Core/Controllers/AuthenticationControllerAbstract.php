<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\User\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Core\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class AuthenticationControllerAbstract
 *
 * @SWG\Definition(
 *     definition="AuthenticationToken",
 *     type="object",
 *     @SWG\Property(
 *         property="token",
 *         type="string",
 *         description="The authentication token"
 *     )
 * )
 *
 * @package App\Http\Core\Controllers
 */
abstract class AuthenticationControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var UserRepositoryContract
     */
    protected $userRepository;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * AuthenticationController constructor.
     * @param UserRepositoryContract $userRepository
     * @param Hasher $hasher
     * @param JWTAuth $auth
     */
    public function __construct(UserRepositoryContract $userRepository, Hasher $hasher, JWTAuth $auth)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->auth = $auth;
    }

    /**
     * Authenticate the user and get the token - or 401
     *
     * Should receive username and password for terminal or email and password for website.
     *
     * @SWG\Post(
     *     path="/auth/login",
     *     summary="Login to get JWT, should receive email and password.",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Auth"},
     *     @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          description="The user's email",
     *          type="string",
     *          format="email"
     *     ),
     *     @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          description="The user's password",
     *          required=true,
     *          type="string",
     *          maxLength=255
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Successful authentication",
     *          @SWG\Schema(ref="#/definitions/AuthenticationToken"),
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
     * @param Requests\Authentication\LoginRequest $request
     * @return JsonResponse
     * @throws JWTException
     */
    public function login(Requests\Authentication\LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (! $token = $this->auth->attempt($credentials)) {
            throw new JWTException('Invalid login credentials.', 401);
        }

        return new JsonResponse([
            'token' => $token,
        ]);
    }

    /**
     * This handles refreshing an authentication token
     *
     * @SWG\Post(
     *     path="/auth/refresh",
     *     summary="Get a refreshed token",
     *     tags={"Auth"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Response(
     *          response=200,
     *          description="New Refreshed token",
     *          @SWG\Schema(ref="#/definitions/AuthenticationToken"),
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
     * @param Request $request
     * @return JsonResponse
     * @throws JWTException
     */
    public function refresh(Request $request)
    {
        $newToken = $this->auth->setRequest($request)->parseToken()->refresh();

        return new JsonResponse([
            'token' => $newToken,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @SWG\Post(
     *     path="/auth/sign-up",
     *     summary="Signs Up a new user",
     *     tags={"Auth","Users"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="incoming-model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/User"),
     *          description="The model to create"
     *     ),
     *     @SWG\Response(
     *          response=201,
     *          description="User created successfully",
     *          @SWG\Schema(ref="#/definitions/AuthenticationToken"),
     *          @SWG\Header(
     *              header="Location",
     *              description="The URL to retrieve the newly created item",
     *              type="string",
     *              format="url"
     *          ),
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
     * @param Requests\Authentication\SignUpRequest $request
     * @return JsonResponse
     */
    public function signUp(Requests\Authentication\SignUpRequest $request)
    {
        $data = $request->json()->all();

        $forcedData = [
            'password' => $this->hasher->make($data['password']),
        ];

        /** @var User $model */
        $model = $this->userRepository->create($data, null, $forcedData);

        $token = $this->auth->fromUser($model);
        return new JsonResponse([
            'token' => $token,
        ], 201);
    }

    /**
     * Handles logging out if there is a valid JWT
     *
     * @SWG\Post(
     *     path="/auth/logout",
     *     summary="Log out",
     *     tags={"Auth"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Response(
     *          response=200,
     *          description="The token has been destroyed",
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
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $this->auth->invalidate($this->auth->getToken());

        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}