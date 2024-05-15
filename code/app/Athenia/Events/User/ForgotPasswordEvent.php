<?php
declare(strict_types=1);

namespace App\Athenia\Events\User;

use App\Models\User\PasswordToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ForgotPasswordEvent
 * @package App\Events\User
 */
class ForgotPasswordEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var PasswordToken
     */
    private $passwordToken;

    /**
     * ForgotPasswordEvent constructor.
     * @param PasswordToken $passwordToken
     */
    public function __construct(PasswordToken $passwordToken)
    {
        $this->passwordToken = $passwordToken;
    }

    /**
     * @return PasswordToken
     */
    public function getPasswordToken(): PasswordToken
    {
        return $this->passwordToken;
    }
}