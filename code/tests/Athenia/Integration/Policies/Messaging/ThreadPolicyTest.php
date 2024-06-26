<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Messaging;

use App\Athenia\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Athenia\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\User\User;
use App\Policies\Messaging\ThreadPolicy;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ThreadPolicyTest
 * @package Tests\Athenia\Integration\Policies\User
 */
final class ThreadPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var ThreadSubjectGateProviderContract|CustomMockInterface
     */
    private $gateProvider;

    /**
     * @var ThreadPolicy
     */
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->gateProvider = mock(ThreadSubjectGateProviderContract::class);
        $this->policy = new ThreadPolicy($this->gateProvider);
    }

    public function testAllBlocksWhenGateNotFound(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testAllBlockWhenAccessingAnotherUser(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testAllBlockWhenGateFails(): void
    {
        $loggedInUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnFalse();

        $this->assertFalse($this->policy->all($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testAllPasses(): void
    {
        $loggedInUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnTrue();

        $this->assertTrue($this->policy->all($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testCreateBlocksWhenGateNotFound(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testCreateBlockWhenAccessingAnotherUser(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testCreateBlockWhenGateFails(): void
    {
        $loggedInUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnFalse();

        $this->assertFalse($this->policy->create($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testCreatePasses(): void
    {
        $loggedInUser = User::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnTrue();

        $this->assertTrue($this->policy->create($loggedInUser, $loggedInUser, 'a_type', 43));
    }
}
