<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\ForgotPassword;

use App\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\User\PasswordToken;
use App\Models\User\User;
use App\Validators\ForgotPassword\UserOwnsTokenValidator;
use Illuminate\Http\Request;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserOwnsValidatorTest
 * @package Tests\Unit\Validators\ForgotPassword
 */
class UserOwnsValidatorTest extends TestCase
{
    /**
     * @var Request|CustomMockInterface
     */
    private $request;

    /**
     * @var UserRepositoryContract|CustomMockInterface
     */
    private $userRepository;

    /**
     * @var PasswordTokenRepositoryContract|CustomMockInterface
     */
    private $passwordTokenRepository;

    /**
     * @var UserOwnsTokenValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = mock(Request::class);
        $this->userRepository = mock(UserRepositoryContract::class);
        $this->passwordTokenRepository = mock(PasswordTokenRepositoryContract::class);

        $this->validator = new UserOwnsTokenValidator(
            $this->request,
            $this->userRepository,
            $this->passwordTokenRepository
        );
    }

    public function testFailsNoEmailInRequest(): void
    {
        $this->request->shouldReceive('input')->once()->with('email', null)->andReturn(null);

        $this->assertFalse($this->validator->validate('token', 'hello'));
    }

    public function testFailsUserNotFound(): void
    {
        $this->request->shouldReceive('input')->once()->with('email', null)->andReturn('test@test.com');

        $this->userRepository->shouldReceive('findByEmail')->with('test@test.com')->andReturn(null);

        $this->assertFalse($this->validator->validate('token', 'hello'));
    }

    public function testFailsTokenNotFound(): void
    {
        $user = new User();

        $this->request->shouldReceive('input')->once()->with('email', null)->andReturn('test@test.com');

        $this->userRepository->shouldReceive('findByEmail')->once()->with('test@test.com')->andReturn($user);

        $this->passwordTokenRepository->shouldReceive('findForUser')->once()
                            ->with($user, 'hello')->andReturn(null);

        $this->assertFalse($this->validator->validate('token', 'hello'));
    }

    public function testPasses(): void
    {
        $user = new User();
        $passwordToken = new PasswordToken();

        $this->request->shouldReceive('input')->once()->with('email', null)->andReturn('test@test.com');

        $this->userRepository->shouldReceive('findByEmail')->once()->with('test@test.com')->andReturn($user);

        $this->passwordTokenRepository->shouldReceive('findForUser')->once()
            ->with($user, 'hello')->andReturn($passwordToken);

        $this->assertTrue($this->validator->validate('token', 'hello'));
    }
}