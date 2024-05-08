<?php
declare(strict_types=1);

namespace App\Policies\Messaging;

use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class ThreadPolicy
 * @package App\Policies\User
 */
class ThreadPolicy extends BasePolicyAbstract
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
     * @param string $threadSubject
     * @param null|int $subjectId
     * @return bool
     */
    public function all(User $loggedInUser, User $requestedUser, string $threadSubject, $subjectId = null)
    {
        $gate = $this->provider->createGate($threadSubject);

        if ($gate == null) {
            return false;
        }

        return $loggedInUser->id == $requestedUser->id && $gate->authorizeSubject($loggedInUser, $subjectId);
    }

    /**
     * Users can create threads only for themselves, and with users that they do not already have a thread with
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param string $threadSubject
     * @param null $subjectId
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser, string $threadSubject, $subjectId = null)
    {
        $gate = $this->provider->createGate($threadSubject);

        if ($gate == null) {
            return false;
        }

        return $loggedInUser->id == $requestedUser->id && $gate->authorizeSubject($loggedInUser, $subjectId);
    }
}