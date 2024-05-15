<?php
declare(strict_types=1);

namespace App\Athenia\Events\User;

use App\Models\User\User;

/**
 * Class SignUpEvent
 * @package App\Events\User
 */
class SignUpEvent
{
    /**
     * @var User
     */
    private $user;

    /**
     * SignUpEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}