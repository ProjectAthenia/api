<?php
declare(strict_types=1);

namespace App\Services\Wiki;

use App\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Contracts\Services\StringHelperServiceContract;
use App\Contracts\Services\Wiki\ArticleModificationApplicationServiceContract;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Models\Wiki\ArticleModification;

/**
 * Class ArticleModificationApplicationService
 * @package App\Services\Wiki
 */
class ArticleModificationApplicationService implements ArticleModificationApplicationServiceContract
{
    /**
     * ArticleModificationApplicationService constructor.
     * @param ArticleIterationRepositoryContract $iterationRepository
     * @param StringHelperServiceContract $stringHelperService
     */
    public function __construct(
        private ArticleIterationRepositoryContract $iterationRepository,
        private StringHelperServiceContract $stringHelperService,
    ){}

    /**
     * Runs all changes needed for the past through article modification
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function applyModification(User $user, ArticleModification $articleModification): ?Article
    {
        switch ($articleModification->action) {
            case 'remove':
                return $this->handleRemoveAction($user, $articleModification);

            case 'add':
                return $this->handleAddAction($user, $articleModification);

            case 'replace':
                return $this->handleReplaceAction($user, $articleModification);

            default:
                return null;
        }
    }

    /**
     * Handles a remove action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleRemoveAction(User $user, ArticleModification $articleModification): ?Article
    {
        $startPosition = $articleModification->start_position;
        $length = $articleModification->length;

        if ($startPosition !== null && $length !== null) {
            $article = $articleModification->article;
            /** @var ArticleIteration $iteration */
            $this->iterationRepository->create([
                'content' => $this->stringHelperService->mbSubstrReplace($article->last_iteration_content, '', $startPosition, $length),
                'created_by_id' => $user->id,
            ], $article);

            return $article->refresh();
        }

        return null;
    }

    /**
     * Handles an add action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleAddAction(User $user, ArticleModification $articleModification): ?Article
    {
        $startPosition = $articleModification->start_position;
        $content = $articleModification->content;

        if ($startPosition !== null && $content) {

            $article = $articleModification->article;

            $existingContent = $article->last_iteration_content ?? "";

            $beginningString = mb_substr($existingContent, 0, $startPosition);
            $endString = mb_substr($existingContent, $startPosition);

            $this->iterationRepository->create([
                'content' => $beginningString . $content . $endString,
                'created_by_id' => $user->id,
            ], $article);

            return $article->refresh();
        }

        return null;
    }

    /**
     * Handles a replace action based on the modification passed through
     *
     * @param User $user
     * @param ArticleModification $articleModification
     * @return Article|null
     */
    public function handleReplaceAction(User $user, ArticleModification $articleModification): ?Article
    {
        $startPosition = $articleModification->start_position;
        $length = $articleModification->length;
        $content = $articleModification->content;

        if ($startPosition !== null && $length !== null && $content) {

            $article = $articleModification->article;

            $this->iterationRepository->create([
                'content' => $this->stringHelperService->mbSubstrReplace($article->last_iteration_content, $content, $startPosition, $length),
                'created_by_id' => $user->id,
            ], $article);

            return $article->refresh();
        }

        return null;
    }
}
