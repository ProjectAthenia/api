<?php
declare(strict_types=1);

namespace App\Policies\User;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\Contact;
use App\Models\User\User;

/**
 * Class ContactPolicy
 * @package App\Policies\User
 */
class ContactPolicy extends BasePolicyAbstract
{
    /**
     * Any logged in user can view their own contacts
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function all(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * Any logged in user can view their own contacts
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * Any logged in user can update a contact they are related to
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Contact $contact
     * @return bool
     */
    public function update(User $loggedInUser, User $requestedUser, Contact $contact)
    {
        return $loggedInUser->id == $requestedUser->id &&
            ($requestedUser->id == $contact->initiated_by_id || $requestedUser->id == $contact->requested_id);
    }

    /**
     * Any logged in user can delete a contact they are related to
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Contact $contact
     * @return bool
     */
    public function delete(User $loggedInUser, User $requestedUser, Contact $contact)
    {
        return $loggedInUser->id == $requestedUser->id &&
            ($requestedUser->id == $contact->initiated_by_id || $requestedUser->id == $contact->requested_id);
    }
}