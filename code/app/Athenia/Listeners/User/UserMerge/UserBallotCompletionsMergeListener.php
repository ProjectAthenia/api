<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;

/**
 * Class UserBallotCompletionsMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserBallotCompletionsMergeListener
{
    /**
     * @var BallotCompletionRepositoryContract
     */
    private $ballotCompletionRepository;

    /**
     * UserBallotCompletionsMergeListener constructor.
     * @param BallotCompletionRepositoryContract $ballotCompletionRepository
     */
    public function __construct(BallotCompletionRepositoryContract $ballotCompletionRepository)
    {
        $this->ballotCompletionRepository = $ballotCompletionRepository;
    }

    /**
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['ballot_completions'] ?? false) {
            foreach ($mergeUser->ballotCompletions as $ballotCompletion) {
                $this->ballotCompletionRepository->update($ballotCompletion, [
                    'user_id' => $mainUser->id,
                ]);
            }
        }
    }
}