<?php
declare(strict_types=1);

namespace App\Policies\Messaging;

use App\Athenia\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User\User;

/**
 * Class MessagePolicy
 * @package App\Policies\User
 */
class MessagePolicy extends BasePolicyAbstract
{
    /**
     * @var ThreadSubjectGateProviderContract
     */
    private $provider;

    /**
     * ThreadPolicy constructor.
     * @param ThreadSubjectGateProviderContract $provider
     */
    public function __construct(ThreadSubjectGateProviderContract $provider)
    {
        $this->provider = $provider;
    }

    /**
     * A user can see all of their threads
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Thread $thread
     * @return bool
     */
    public function all(User $loggedInUser, User $requestedUser, Thread $thread)
    {
        $gate = $this->provider->createGate($thread->subject_type);

        if ($gate == null) {
            return false;
        }

        return $loggedInUser->id == $requestedUser->id && $gate->authorizeThread($loggedInUser, $thread);
    }

    /**
     * Users can create message in threads they are apart of
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Thread $thread
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser, Thread $thread)
    {
        $gate = $this->provider->createGate($thread->subject_type);

        if ($gate == null) {
            return false;
        }

        return $loggedInUser->id == $requestedUser->id && $gate->authorizeThread($loggedInUser, $thread);
    }

    /**
     * Only users that have been sent a message can update the message
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Thread $thread
     * @param Message $message
     * @return bool
     */
    public function update(User $loggedInUser, User $requestedUser, Thread $thread, Message $message)
    {
        $gate = $this->provider->createGate($thread->subject_type);

        if ($gate == null) {
            return false;
        }

        return $loggedInUser->id == $requestedUser->id && $gate->authorizeThread($loggedInUser, $thread) &&
            $thread->id == $message->thread_id && $message->to_id == $loggedInUser->id;
    }
}