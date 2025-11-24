<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User;

use App\Athenia\Contracts\Repositories\User\InvitationTokenRepositoryContract;
use App\Athenia\Events\User\InvitationAcceptedEvent;
use Illuminate\Support\Carbon;

/**
 * Class InvitationAcceptedListener
 * @package App\Athenia\Listeners\User
 */
class InvitationAcceptedListener
{
    /**
     * @var InvitationTokenRepositoryContract
     */
    private InvitationTokenRepositoryContract $invitationTokenRepository;

    /**
     * InvitationAcceptedListener constructor.
     * @param InvitationTokenRepositoryContract $invitationTokenRepository
     */
    public function __construct(InvitationTokenRepositoryContract $invitationTokenRepository)
    {
        $this->invitationTokenRepository = $invitationTokenRepository;
    }

    /**
     * Handles the invitation accepted event by marking the token as used
     * and adding the associated role to the user if present
     *
     * @param InvitationAcceptedEvent $event
     */
    public function handle(InvitationAcceptedEvent $event): void
    {
        $user = $event->getUser();
        $invitationToken = $event->getInvitationToken();

        // Mark the invitation token as used
        $this->invitationTokenRepository->update($invitationToken, [
            'used_at' => Carbon::now(),
        ]);

        // If the invitation token has a role, add it to the user
        if ($invitationToken->role_id) {
            $user->roles()->attach($invitationToken->role_id);
        }
    }
}
