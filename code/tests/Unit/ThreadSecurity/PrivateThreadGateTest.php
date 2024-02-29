<?php
declare(strict_types=1);

namespace Tests\Unit\ThreadSecurity;

use App\Models\User\Thread;
use App\Models\User\User;
use App\ThreadSecurity\PrivateThreadGate;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class PrivateThreadGateTest
 * @package Tests\Unit\ThreadSecurity
 */
final class PrivateThreadGateTest extends TestCase
{
    public function testAuthorizeSubject(): void
    {
        $gate = new PrivateThreadGate();

        $this->assertTrue($gate->authorizeSubject(new User()));
    }

    public function testAuthorizeThread(): void
    {
        $gate = new PrivateThreadGate();

        $thread =  new Thread([
            'users' => new Collection([]),
        ]);

        $user = new User();
        $user->id = 453;

        $this->assertFalse($gate->authorizeThread($user, $thread));

        $thread->users->push($user);
        $this->assertTrue($gate->authorizeThread($user, $thread));
    }
}