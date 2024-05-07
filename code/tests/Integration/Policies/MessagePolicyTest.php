<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use App\Policies\Messaging\MessagePolicy;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MessagePolicyTest
 * @package Tests\Integration\Policies\User
 */
final class MessagePolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var ThreadSubjectGateProviderContract|CustomMockInterface
     */
    private $gateProvider;

    /**
     * @var MessagePolicy
     */
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->gateProvider = mock(ThreadSubjectGateProviderContract::class);
        $this->policy = new MessagePolicy($this->gateProvider);
    }

    public function testAllBlocksWhenGateNotFound(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, $thread));
    }

    public function testAllBlockWhenAccessingAnotherUser(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, $thread));
    }

    public function testAllBlockWhenGateFails(): void
    {
        $loggedInUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->all($loggedInUser, $loggedInUser, $thread));
    }

    public function testAllPasses(): void
    {
        $loggedInUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->all($loggedInUser, $loggedInUser, $thread));
    }

    public function testCreateBlocksWhenGateNotFound(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, $thread));
    }

    public function testCreateBlockWhenAccessingAnotherUser(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, $thread));
    }

    public function testCreateBlockWhenGateFails(): void
    {
        $loggedInUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->create($loggedInUser, $loggedInUser, $thread));
    }

    public function testCreatePasses(): void
    {
        $loggedInUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->create($loggedInUser, $loggedInUser, $thread));
    }

    public function testUpdateBlocksWhenGateNotFound(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $message = Message::factory()->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->update($loggedInUser, $requestedUser, $thread, $message));
    }

    public function testUpdateBlocksUserMismatch(): void
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();
        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $message = Message::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->update($loggedInUser, $requestedUser, $thread, $message));
    }

    public function testUpdateBlockWhenGateFails(): void
    {
        $loggedInUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $message = Message::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->update($loggedInUser, $loggedInUser, $thread, $message));
    }

    public function testUpdateBlocksMessageNotInThread(): void
    {
        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertFalse($this->policy->update($user, $user, $thread, $message));
    }

    public function testUpdateBlocksUserNotSentMessage(): void
    {
        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'thread_id' => $thread->id,
            'to_id' => User::factory()->create()->id,
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertFalse($this->policy->update($user, $user, $thread, $message));
    }

    public function testUpdatePasses(): void
    {
        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'thread_id' => $thread->id,
            'to_id' => $user->id,
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->update($user, $user, $thread, $message));
    }
}
