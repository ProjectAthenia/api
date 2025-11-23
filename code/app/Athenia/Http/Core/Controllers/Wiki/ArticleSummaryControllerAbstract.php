<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleSummaryRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleSummary;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleSummaryControllerAbstract
 * @package App\Athenia\Http\Core\Controllers\Wiki
 */
abstract class ArticleSummaryControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var ArticleSummaryRepositoryContract
     */
    private ArticleSummaryRepositoryContract $repository;

    /**
     * ArticleSummaryController constructor.
     * @param ArticleSummaryRepositoryContract $repository
     */
    public function __construct(ArticleSummaryRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display the summary for the article
     *
     * @param Requests\Wiki\ArticleSummary\ViewRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function show(Requests\Wiki\ArticleSummary\ViewRequest $request, Article $article): JsonResponse
    {
        $summary = $article->articleSummary;

        if (!$summary) {
            return new JsonResponse([
                'message' => 'Article summary not found.'
            ], 404);
        }

        return new JsonResponse($summary, 200);
    }

    /**
     * Create a new summary for the article
     *
     * @param Requests\Wiki\ArticleSummary\StoreRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function store(Requests\Wiki\ArticleSummary\StoreRequest $request, Article $article): JsonResponse
    {
        $data = $request->json()->all();
        $data['article_id'] = $article->id;

        /** @var ArticleSummary $model */
        $model = $this->repository->create($data);

        return new JsonResponse($model, 201);
    }

    /**
     * Update the article summary
     *
     * @param Requests\Wiki\ArticleSummary\UpdateRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function update(Requests\Wiki\ArticleSummary\UpdateRequest $request, Article $article): JsonResponse
    {
        $summary = $article->articleSummary;

        if (!$summary) {
            return new JsonResponse([
                'message' => 'Article summary not found.'
            ], 404);
        }

        $data = $request->json()->all();

        /** @var ArticleSummary $updated */
        $updated = $this->repository->update($summary, $data);

        return new JsonResponse($updated, 200);
    }

    /**
     * Delete the article summary
     *
     * @param Requests\Wiki\ArticleSummary\DeleteRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Requests\Wiki\ArticleSummary\DeleteRequest $request, Article $article): JsonResponse
    {
        $summary = $article->articleSummary;

        if (!$summary) {
            return new JsonResponse([
                'message' => 'Article summary not found.'
            ], 404);
        }

        $this->repository->delete($summary);

        return new JsonResponse(null, 204);
    }
}
