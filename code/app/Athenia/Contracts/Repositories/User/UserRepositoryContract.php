<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\User;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\User;
use Illuminate\Support\Collection;

/**
 * Interface UserRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    /**
     * Attempts to look up a user by email address, and returns null if we cannot find one
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email) : ?User;

    /**
     * Finds all system users in the system
     *
     * Creates a new user if one is not found
     *
     * @return Collection
     */
    public function findSuperAdmins(): Collection;
}
