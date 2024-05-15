<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;

/**
 * Class UserCreatedArticlesMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserCreatedArticlesMergeListener
{
    /**
     * @var ArticleRepositoryContract
     */
    private $articleRepository;

    /**
     * UserCreatedArticlesMergeListener constructor.
     * @param ArticleRepositoryContract $articleRepository
     */
    public function __construct(ArticleRepositoryContract $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['created_articles'] ?? false) {
            foreach ($mergeUser->createdArticles as $article) {
                $this->articleRepository->update($article, [
                    'created_by_id' => $mainUser->id,
                ]);
            }
        }
    }
}