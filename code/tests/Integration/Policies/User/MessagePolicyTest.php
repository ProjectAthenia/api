<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\User\Message;
use App\Models\User\Thread;
use App\Models\User\User;
use App\Policies\User\MessagePolicy;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MessagePolicyTest
 * @package Tests\Integration\Policies\User
 */
class MessagePolicyTest extends TestCase
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

    public function testAllBlocksWhenGateNotFound()
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, $thread));
    }

    public function testAllBlockWhenAccessingAnotherUser()
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

    public function testAllBlockWhenGateFails()
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

    public function testAllPasses()
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

    public function testCreateBlocksWhenGateNotFound()
    {
        $loggedInUser = User::factory()->create();
        $requestedUser = User::factory()->create();

        $thread = Thread::factory()->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, $thread));
    }

    public function testCreateBlockWhenAccessingAnotherUser()
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

    public function testCreateBlockWhenGateFails()
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

    public function testCreatePasses()
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

    public function testUpdateBlocksWhenGateNotFound()
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

    public function testUpdateBlocksUserMismatch()
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

    public function testUpdateBlockWhenGateFails()
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

    public function testUpdateBlocksMessageNotInThread()
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

    public function testUpdateBlocksUserNotSentMessage()
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

    public function testUpdatePasses()
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
