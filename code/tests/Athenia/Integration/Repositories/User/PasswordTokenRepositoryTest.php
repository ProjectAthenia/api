<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Events\User\ForgotPasswordEvent;
use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\User\PasswordTokenRepository;
use App\Models\User\PasswordToken;
use App\Models\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class PasswordTokenRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
final class PasswordTokenRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var Dispatcher|CustomMockInterface
     */
    private $dispatcher;

    /**
     * @var TokenGenerationServiceContract|CustomMockInterface
     */
    private $tokenGenerationService;

    /**
     * @var PasswordTokenRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = mock(Dispatcher::class);
        $this->tokenGenerationService = mock(TokenGenerationServiceContract::class);
        $this->repository = new PasswordTokenRepository(
            new PasswordToken(),
            $this->getGenericLogMock(),
            $this->dispatcher,
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

        $this->repository->delete(new PasswordToken());
    }

    public function testUpdateThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->update(new PasswordToken(), []);
    }

    public function testCreateSuccess(): void
    {
        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with(\Mockery::on(function (ForgotPasswordEvent $event) {
                return true;
            })
        );

        $user = User::factory()->create();

        /** @var PasswordToken $passwordToken */
        $passwordToken = $this->repository->create([
            'token' => 'hello',
        ], $user);

        $this->assertEquals('hello', $passwordToken->token);
        $this->assertEquals($user->id, $passwordToken->user_id);
    }

    public function testFindForUser(): void
    {
        $user = User::factory()->create();
        $passwordToken = PasswordToken::factory()->create([
            'token' => '1234',
            'user_id' => $user->id,
        ]);

        $this->assertEquals($passwordToken->id, $this->repository->findForUser($user, '1234')->id);
        $this->assertNull($this->repository->findForUser($user, '12345'));
    }

    public function testGenerateUniqueTokenSuccess(): void
    {
        $user = User::factory()->create();

        $this->tokenGenerationService->shouldReceive('generateToken')->once()->andReturn('12345');

        $this->assertEquals('12345', $this->repository->generateUniqueToken($user));
    }

    public function testGenerateUniqueTokenThrowsException(): void
    {
        $user = User::factory()->create();
        PasswordToken::factory()->create([
            'user_id' => $user->id,
            'token' => '12345',
        ]);

        $this->tokenGenerationService->shouldReceive('generateToken')->times(5)->andReturn('12345');
        $this->expectException(\OverflowException::class);

        $this->repository->generateUniqueToken($user);
    }
}
