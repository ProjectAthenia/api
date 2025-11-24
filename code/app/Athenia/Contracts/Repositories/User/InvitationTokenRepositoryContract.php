<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\User;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Athenia\Models\User\InvitationToken;

/**
 * Interface InvitationTokenRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface InvitationTokenRepositoryContract extends BaseRepositoryContract
{
    /**
     * Generates a unique token, or throws an exception if it cannot do so.
     *
     * @throws \OverflowException
     * @return string
     */
    public function generateUniqueToken(): string;

    /**
     * Finds an invitation token by its token string
     *
     * @param string $token
     * @return InvitationToken|null
     */
    public function findByToken(string $token): ?InvitationToken;
}
