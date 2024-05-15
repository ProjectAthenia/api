<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\User;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\PasswordToken;
use App\Models\User\User;

/**
 * Interface PasswordTokenRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface PasswordTokenRepositoryContract extends BaseRepositoryContract
{
    /**
     * Generates a unique token for a user, or throws an exception if it cannot do so.
     *
     * @param User $user
     * @throws \OverflowException
     * @return string
     */
    public function generateUniqueToken(User $user) : string;

    /**
     * Searches for a password token model owned by a user with a token
     *
     * @param User $user
     * @param string $token
     * @return PasswordToken|null
     */
    public function findForUser(User $user, string $token) : ?PasswordToken;
}