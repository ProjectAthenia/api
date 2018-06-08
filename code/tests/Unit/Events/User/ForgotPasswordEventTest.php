<?php
declare(strict_types=1);

namespace Tests\Unit\Events\User;

use App\Events\User\ForgotPasswordEvent;
use App\Models\User\PasswordToken;
use Tests\TestCase;

/**
 * Class ForgotPasswordEventTest
 * @package Tests\Unit\Events\User
 */
class ForgotPasswordEventTest extends TestCase
{
    public function testGetPasswordToken()
    {
        $passwordToken = new PasswordToken();

        $event = new ForgotPasswordEvent($passwordToken);

        $this->assertEquals($passwordToken, $event->getPasswordToken());
    }
}