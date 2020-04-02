<?php
declare(strict_types=1);

namespace Tests\Unit\Events\User;

use App\Events\User\UserMergeEvent;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class UserMergeEventTest
 * @package Tests\Unit\Events\User
 */
class UserMergeEventTest extends TestCase
{
    public function testGetMainUser()
    {
        $user = new User([
            'email' => 'something@something.something',
        ]);

        $event = new UserMergeEvent($user, new User(), []);

        $this->assertEquals($user, $event->getMainUser());
    }

    public function testGetMergeUser()
    {
        $user = new User([
            'email' => 'something@something.something',
        ]);

        $event = new UserMergeEvent(new User(), $user, []);

        $this->assertEquals($user, $event->getMergeUser());
    }

    public function testGetMergeOptions()
    {
        $options = [
            'email' => true,
        ];

        $event = new UserMergeEvent(new User(), new User(), $options);

        $this->assertEquals($options, $event->getMergeOptions());
    }
}