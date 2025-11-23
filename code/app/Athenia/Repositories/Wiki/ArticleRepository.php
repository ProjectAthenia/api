<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Statistic\Statistic;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleRepository
 * @package App\Repositories\Wiki
 */
class ArticleRepository extends BaseRepositoryAbstract implements ArticleRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Delete;
    use CanGetAndUnset;

    /**
     * ArticleRepository constructor.
     * @param Article $model
     * @param LogContract $log
     */
    public function __construct(Article $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Override create to handle categories sync
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = []): BaseModelAbstract
    {
        $categories = $this->getAndUnset($data, 'categories');

        /** @var Article $article */
        $article = parent::create($data, $relatedModel, $forcedValues);

        if ($categories !== null) {
            $syncData = collect($categories)->mapWithKeys(fn($cat) => [
                $cat['category_id'] => array_filter(['relevance' => $cat['relevance'] ?? null])
            ])->toArray();
            $article->categories()->sync($syncData);
        }

        return $article;
    }

    /**
     * Override update to handle categories sync
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $categories = $this->getAndUnset($data, 'categories');

        /** @var Article $article */
        $article = parent::update($model, $data, $forcedValues);

        if ($categories !== null) {
            $syncData = collect($categories)->mapWithKeys(fn($cat) => [
                $cat['category_id'] => array_filter(['relevance' => $cat['relevance'] ?? null])
            ])->toArray();
            $article->categories()->sync($syncData);
        }

        return $article;
    }

    /**
     * Selects an article for a user based on their note completion status and article statistics.
     *
     * Priority order:
     * 1. Articles where user has NO note started (never returns articles with completed notes)
     * 2. Articles where user has an incomplete note
     *
     * Within each priority group, orders by:
     * - Lowest total_completed_notes statistic (fewest completions by all users)
     * - Lowest total_notes statistic (fewest notes started by all users)
     *
     * @param User $user
     * @return Article|null
     */
    public function selectArticleForUser(User $user): ?Article
    {
        $totalNotesStatId = Statistic::where('name', 'total_notes')->value('id');
        $totalCompletedNotesStatId = Statistic::where('name', 'total_completed_notes')->value('id');

        return Article::query()
            ->select('articles.*')
            ->leftJoin('article_notes as user_notes', function($join) use ($user) {
                $join->on('articles.id', '=', 'user_notes.article_id')
                     ->where('user_notes.user_id', '=', $user->id)
                     ->whereNull('user_notes.deleted_at');
            })
            ->leftJoin('target_statistics as completed_stats', function($join) use ($totalCompletedNotesStatId) {
                $join->on('articles.id', '=', 'completed_stats.target_id')
                     ->where('completed_stats.target_type', '=', 'article')
                     ->where('completed_stats.statistic_id', '=', $totalCompletedNotesStatId);
            })
            ->leftJoin('target_statistics as total_stats', function($join) use ($totalNotesStatId) {
                $join->on('articles.id', '=', 'total_stats.target_id')
                     ->where('total_stats.target_type', '=', 'article')
                     ->where('total_stats.statistic_id', '=', $totalNotesStatId);
            })
            // Exclude articles where user has completed a note
            ->where(function($query) {
                $query->whereNull('user_notes.id')
                      ->orWhereNull('user_notes.completed_at');
            })
            // Order by priority: no note (1) before incomplete note (2)
            ->orderByRaw('CASE WHEN user_notes.id IS NULL THEN 1 ELSE 2 END ASC')
            // Then order by lowest completed notes count
            ->orderByRaw('COALESCE(CAST(JSON_EXTRACT(completed_stats.result, "$.total") AS UNSIGNED), 0) ASC')
            // Then order by lowest total notes count
            ->orderByRaw('COALESCE(CAST(JSON_EXTRACT(total_stats.result, "$.total") AS UNSIGNED), 0) ASC')
            // Finally order by article ID for deterministic results
            ->orderBy('articles.id', 'ASC')
            ->first();
    }
}