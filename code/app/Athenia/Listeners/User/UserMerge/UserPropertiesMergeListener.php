<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;
use Carbon\Carbon;

/**
 * Class UserMergeListener
 * @package App\Listeners\User
 */
class UserPropertiesMergeListener
{
    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * UserMergeListener constructor.
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Makes sure to merge all user data properly
     *
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        $mergeData = [];

        foreach ($mergeOptions as $field => $merge) {
            if ($merge && $mergeUser->getAttributeValue($field)) {
                $mergeData[$field] = $mergeUser->getAttributeValue($field);
            }
        }

        if ($mergeData) {
            $this->userRepository->update($mainUser, $mergeData);
        }

        $this->userRepository->update($mergeUser, [
            'merged_to_id' => $mainUser->id,
            'deleted_at' => Carbon::now(),
        ]);
    }
}