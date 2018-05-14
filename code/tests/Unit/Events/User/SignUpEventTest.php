<?php
declare(strict_types=1);

namespace Tests\Unit\Events\User;

use App\Events\User\SignUpEvent;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class SignUpEventTest
 * @package Tests\Unit\Events\User
 */
class SignUpEventTest extends TestCase
{
    public function testGetUser()
    {
        $user = new User();

        $event = new SignUpEvent($user);

        $this->assertEquals($user, $event->getUser());
    }
}