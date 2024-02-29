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
final class SignUpEventTest extends TestCase
{
    public function testGetUser(): void
    {
        $user = new User();

        $event = new SignUpEvent($user);

        $this->assertEquals($user, $event->getUser());
    }
}