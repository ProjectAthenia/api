<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Article;

use App\Athenia\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Wiki\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleVersionControllerAbstract
 * @package App\Http\Core\Controllers\Article
 */
abstract class ArticleVersionControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ArticleVersionRepositoryContract
     */
    private $repository;

    /**
     * ArticleVersionController constructor.
     * @param ArticleVersionRepositoryContract $repository
     */
    public function __construct(ArticleVersionRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Loads all created version for the related article
     *
     * @param Requests\Article\ArticleVersion\IndexRequest $request
     * @param Article $article
     * @return LengthAwarePaginator
     */
    public function index(Requests\Article\ArticleVersion\IndexRequest $request, Article $article)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$article], (int)$request->input('page', 1));
    }

    /**
     * Creates a new article version
     *
     * @param Requests\Article\ArticleVersion\StoreRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function store(Requests\Article\ArticleVersion\StoreRequest $request, Article $article) : JsonResponse
    {
        $data = $request->json()->all();

        return new JsonResponse($this->repository->create($data, $article), 201);
    }
}