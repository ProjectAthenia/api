<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\User;

use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\User\InvitationTokenRepository;
use App\Models\Role;
use App\Models\User\InvitationToken;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class InvitationTokenRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\User
 */
final class InvitationTokenRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var TokenGenerationServiceContract|CustomMockInterface
     */
    private $tokenGenerationService;

    /**
     * @var InvitationTokenRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->tokenGenerationService = mock(TokenGenerationServiceContract::class);
        $this->repository = new InvitationTokenRepository(
            new InvitationToken(),
            $this->getGenericLogMock(),
            $this->tokenGenerationService
        );
    }

    public function testFindAllThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findAll();
    }

    public function testFindOrFailThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findOrFail(1);
    }

    public function testDeleteThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new InvitationToken());
    }

    public function testCreateSuccess(): void
    {
        $role = Role::find(Role::ARTICLE_EDITOR);

        /** @var InvitationToken $invitationToken */
        $invitationToken = $this->repository->create([
            'token' => 'hello-world',
            'role_id' => $role->id,
        ]);

        $this->assertEquals('hello-world', $invitationToken->token);
        $this->assertEquals($role->id, $invitationToken->role_id);
        $this->assertNull($invitationToken->used_at);
    }

    public function testCreateSuccessWithoutRole(): void
    {
        /** @var InvitationToken $invitationToken */
        $invitationToken = $this->repository->create([
            'token' => 'hello-world',
        ]);

        $this->assertEquals('hello-world', $invitationToken->token);
        $this->assertNull($invitationToken->role_id);
        $this->assertNull($invitationToken->used_at);
    }

    public function testUpdateSuccess(): void
    {
        $invitationToken = InvitationToken::factory()->create([
            'token' => 'test-token',
            'used_at' => null,
        ]);

        $this->assertNull($invitationToken->used_at);

        $this->repository->update($invitationToken, [
            'used_at' => now(),
        ]);

        $invitationToken->refresh();
        $this->assertNotNull($invitationToken->used_at);
    }

    public function testFindByToken(): void
    {
        $invitationToken = InvitationToken::factory()->create([
            'token' => '1234',
        ]);

        $this->assertEquals($invitationToken->id, $this->repository->findByToken('1234')->id);
        $this->assertNull($this->repository->findByToken('12345'));
    }

    public function testGenerateUniqueTokenSuccess(): void
    {
        $this->tokenGenerationService->shouldReceive('generateToken')->once()->andReturn('unique-token-12345');

        $this->assertEquals('unique-token-12345', $this->repository->generateUniqueToken());
    }

    public function testGenerateUniqueTokenThrowsException(): void
    {
        InvitationToken::factory()->create([
            'token' => '12345',
        ]);

        $this->tokenGenerationService->shouldReceive('generateToken')->times(5)->andReturn('12345');
        $this->expectException(\OverflowException::class);

        $this->repository->generateUniqueToken();
    }
}
