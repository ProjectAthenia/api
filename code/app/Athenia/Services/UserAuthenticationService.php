<?php
declare(strict_types=1);

namespace App\Athenia\Services;

use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Exceptions\AuthenticationException;
use App\Athenia\Exceptions\NotImplementedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class UserAuthenticationService
 * @package App\Services
 */
class UserAuthenticationService implements UserProvider
{
    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * UserAuthenticationService constructor.
     * @param UserRepositoryContract $userRepository
     * @param Hasher $hasher
     */
    public function __construct(UserRepositoryContract $userRepository, Hasher $hasher)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        try {
            return $this->userRepository->findOrFail($identifier);
        }
        catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new NotImplementedException();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new NotImplementedException();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (!empty($credentials['email'])) {
            return $this->userRepository->findByEmail($credentials['email']);
        }

        throw new AuthenticationException('No valid identifying credential.');
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }
}