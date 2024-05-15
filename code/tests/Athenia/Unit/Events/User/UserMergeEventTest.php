<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\User;

use App\Athenia\Events\User\UserMergeEvent;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class UserMergeEventTest
 * @package Tests\Athenia\Unit\Events\User
 */
final class UserMergeEventTest extends TestCase
{
    public function testGetMainUser(): void
    {
        $user = new User([
            'email' => 'something@something.something',
        ]);

        $event = new UserMergeEvent($user, new User(), []);

        $this->assertEquals($user, $event->getMainUser());
    }

    public function testGetMergeUser(): void
    {
        $user = new User([
            'email' => 'something@something.something',
        ]);

        $event = new UserMergeEvent(new User(), $user, []);

        $this->assertEquals($user, $event->getMergeUser());
    }

    public function testGetMergeOptions(): void
    {
        $options = [
            'email' => true,
        ];

        $event = new UserMergeEvent(new User(), new User(), $options);

        $this->assertEquals($options, $event->getMergeOptions());
    }
}