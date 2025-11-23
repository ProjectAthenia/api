<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\User;

use App\Athenia\Contracts\Repositories\User\ArticleNoteRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\User\ArticleNote;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleNoteControllerAbstract
 * @package App\Athenia\Http\Core\Controllers\User
 */
abstract class ArticleNoteControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ArticleNoteRepositoryContract
     */
    private ArticleNoteRepositoryContract $repository;

    /**
     * ArticleNoteController constructor.
     * @param ArticleNoteRepositoryContract $repository
     */
    public function __construct(ArticleNoteRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\User\ArticleNote\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\ArticleNote\IndexRequest $request, User $user): LengthAwarePaginator
    {
        return $this->repository->findAll(
            $this->filter($request),
            $this->search($request),
            $this->order($request),
            $this->expand($request),
            $this->limit($request),
            [$user],
            (int)$request->input('page', 1)
        );
    }

    /**
     * @param Requests\User\ArticleNote\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\ArticleNote\StoreRequest $request, User $user): JsonResponse
    {
        $data = $request->json()->all();
        $data['user_id'] = $user->id;

        /** @var ArticleNote $model */
        $model = $this->repository->create($data);

        return new JsonResponse($model, 201);
    }

    /**
     * @param Requests\User\ArticleNote\ViewRequest $request
     * @param User $user
     * @param ArticleNote $articleNote
     * @return ArticleNote
     */
    public function show(Requests\User\ArticleNote\ViewRequest $request, User $user, ArticleNote $articleNote): ArticleNote
    {
        return $articleNote;
    }

    /**
     * @param Requests\User\ArticleNote\UpdateRequest $request
     * @param User $user
     * @param ArticleNote $articleNote
     * @return BaseModelAbstract
     */
    public function update(Requests\User\ArticleNote\UpdateRequest $request, User $user, ArticleNote $articleNote): BaseModelAbstract
    {
        $data = $request->json()->all();

        return $this->repository->update($articleNote, $data);
    }

    /**
     * @param Requests\User\ArticleNote\DeleteRequest $request
     * @param User $user
     * @param ArticleNote $articleNote
     * @return JsonResponse
     */
    public function destroy(Requests\User\ArticleNote\DeleteRequest $request, User $user, ArticleNote $articleNote): JsonResponse
    {
        $this->repository->delete($articleNote);

        return new JsonResponse(null, 204);
    }
}
