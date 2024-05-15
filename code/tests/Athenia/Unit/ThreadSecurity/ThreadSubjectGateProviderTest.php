<?php
declare(strict_types=1);

namespace Tests\Unit\ThreadSecurity;

use App\Athenia\ThreadSecurity\GeneralThreadGate;
use App\Athenia\ThreadSecurity\PrivateThreadGate;
use App\Athenia\ThreadSecurity\ThreadSubjectGateProvider;
use Illuminate\Contracts\Foundation\Application;
use Tests\TestCase;

/**
 * Class ThreadSubjectGateProviderTest
 * @package Tests\Unit\ThreadSecurity
 */
final class ThreadSubjectGateProviderTest extends TestCase
{
    public function testCreateGate(): void
    {
        $provider = new ThreadSubjectGateProvider(mock(Application::class));

        $result = $provider->createGate('general');
        $this->assertInstanceOf(GeneralThreadGate::class, $result);

        $result = $provider->createGate('private_message');
        $this->assertInstanceOf(PrivateThreadGate::class, $result);

        $result = $provider->createGate('rioth');
        $this->assertNull($result);
    }
}