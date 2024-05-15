<?php
declare(strict_types=1);

namespace Tests\Unit\ThreadSecurity;

use App\Athenia\ThreadSecurity\GeneralThreadGate;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class GeneralThreadGateTest
 * @package Tests\Unit\ThreadSecurity
 */
final class GeneralThreadGateTest extends TestCase
{
    public function testAuthorizeSubject(): void
    {
        $gate = new GeneralThreadGate();

        $this->assertTrue($gate->authorizeSubject(new User()));
    }

    public function testAuthorizeThread(): void
    {
        $gate = new GeneralThreadGate();

        $this->assertTrue($gate->authorizeThread(new User(), new Thread()));
    }
}