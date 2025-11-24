<?php
declare(strict_types=1);

namespace App\Athenia\Events\User;

use App\Athenia\Models\User\InvitationToken;
use App\Models\User\User;

/**
 * Class InvitationAcceptedEvent
 * @package App\Events\User
 */
class InvitationAcceptedEvent
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var InvitationToken
     */
    private InvitationToken $invitationToken;

    /**
     * InvitationAcceptedEvent constructor.
     * @param User $user
     * @param InvitationToken $invitationToken
     */
    public function __construct(User $user, InvitationToken $invitationToken)
    {
        $this->user = $user;
        $this->invitationToken = $invitationToken;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return InvitationToken
     */
    public function getInvitationToken(): InvitationToken
    {
        return $this->invitationToken;
    }
}
