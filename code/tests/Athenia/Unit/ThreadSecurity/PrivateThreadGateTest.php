<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\ThreadSecurity;

use App\Athenia\ThreadSecurity\PrivateThreadGate;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class PrivateThreadGateTest
 * @package Tests\Athenia\Unit\ThreadSecurity
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