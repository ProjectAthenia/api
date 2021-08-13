<?php
declare(strict_types=1);

namespace App\Listeners\User\UserMerge;

use App\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Events\User\UserMergeEvent;

/**
 * Class UserCreatedIterationsMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserCreatedIterationsMergeListener
{
    /**
     * @var ArticleIterationRepositoryContract
     */
    private $iterationRepository;

    /**
     * UserCreatedIterationsMergeListener constructor.
     * @param ArticleIterationRepositoryContract $iterationRepository
     */
    public function __construct(ArticleIterationRepositoryContract $iterationRepository)
    {
        $this->iterationRepository = $iterationRepository;
    }

    /**
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['created_iterations'] ?? false) {
            foreach ($mergeUser->createdIterations as $iteration) {
                $this->iterationRepository->update($iteration, [
                    'created_by_id' => $mainUser->id,
                ]);
            }
        }
    }
}
