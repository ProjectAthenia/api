<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\User;

use App\Contracts\Repositories\User\ProfileImageRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Requests;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Mimey\MimeTypes;

/**
 * Class ProfileImageControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class ProfileImageControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var ProfileImageRepositoryContract
     */
    private $repository;

    /**
     * @var MimeTypes
     */
    private $mimeTypes;

    /**
     * ProfileImagesController constructor.
     * @param ProfileImageRepositoryContract $repository
     * @param MimeTypes $mimeTypes
     */
    public function __construct(ProfileImageRepositoryContract $repository, MimeTypes $mimeTypes)
    {
        $this->repository = $repository;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @SWG\Post(
     *     path="/users/{user_id}/profile-images",
     *     summary="Create a new user profile image",
     *     tags={"Users","ProfileImages"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the owning model"
     *     ),
     *     @SWG\Parameter(
     *          name="model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/ProfileImage"),
     *          description="The model to create"
     *     ),
     *     @SWG\Response(
     *          response=201,
     *          description="Model created successfully",
     *          @SWG\Schema(ref="#/definitions/ProfileImage"),
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
     * @param Requests\User\ProfileImage\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\ProfileImage\StoreRequest $request, User $user)
    {
        $data = [];

        $data['file_contents'] = $request->getDecodedContents();
        $data['file_extension'] = $this->mimeTypes->getExtension($request->getFileMimeType());

        $data['user_id'] = $user->id;

        $model = $this->repository->create($data, $user);
        return new JsonResponse($model, 201);
    }
}