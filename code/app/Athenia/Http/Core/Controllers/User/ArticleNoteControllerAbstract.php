<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\User;

use App\Athenia\Contracts\Repositories\User\ArticleNoteRepositoryContract;
use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
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
     * @var ArticleRepositoryContract
     */
    private ArticleRepositoryContract $articleRepository;

    /**
     * ArticleNoteController constructor.
     * @param ArticleNoteRepositoryContract $repository
     * @param ArticleRepositoryContract $articleRepository
     */
    public function __construct(ArticleNoteRepositoryContract $repository, ArticleRepositoryContract $articleRepository)
    {
        $this->repository = $repository;
        $this->articleRepository = $articleRepository;
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

    /**
     * Selects a random article for the user and creates or retrieves an article note
     *
     * @param Requests\User\ArticleNote\RandomArticleRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function randomArticle(Requests\User\ArticleNote\RandomArticleRequest $request, User $user): JsonResponse
    {
        $article = $this->articleRepository->selectArticleForUser($user);

        if (!$article) {
            return new JsonResponse([
                'message' => 'No available articles found.'
            ], 404);
        }

        // Check if a note already exists for this article
        $existingNote = ArticleNote::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existingNote) {
            $existingNote->load('article');
            return new JsonResponse($existingNote, 200);
        }

        /** @var ArticleNote $articleNote */
        $articleNote = $this->repository->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);

        $articleNote->load('article');

        return new JsonResponse($articleNote, 201);
    }
}
