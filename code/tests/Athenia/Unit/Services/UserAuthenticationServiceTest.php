<?php
declare(strict_types=1);

namespace Tests\Unit\Services\Domain;

use Illuminate\Contracts\Hashing\Hasher;
use App\Athenia\Exceptions\AuthenticationException;
use App\Models\User\User;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Services\UserAuthenticationService;
use Tests\TestCase;

/**
 * Class UserAuthenticationServiceTest
 * @package Tests\Unit\Services\Domain
 */
class UserAuthenticationServiceTest extends TestCase
{
    public function testRetrieveById()
    {
        $user = new User();
        $userRepositoryMock = mock(UserRepositoryContract::class);
        $userRepositoryMock->shouldReceive('findOrFail')->once()->with(12)->andReturn($user);
        $hasherMock = mock(Hasher::class);
        $service = new UserAuthenticationService($hasherMock, $userRepositoryMock);

        $this->assertEquals($user, $service->retrieveById(12));
    }

    public function testRetrieveByEmailCredential()
    {
        $user = new User();

        $userRepositoryMock = mock(UserRepositoryContract::class);
        $userRepositoryMock->shouldReceive('findByEmail')->once()->with('guy@smiley.com')->andReturn($user);
        $hasherMock = mock(Hasher::class);
        $service = new UserAuthenticationService($hasherMock, $userRepositoryMock);

        $this->assertEquals($user, $service->retrieveByCredentials(['email'=>'guy@smiley.com']));
    }

    public function testRetrieveByCredentialMissingEmailUsernameFails()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('No valid identifying credential.');
        $userRepositoryMock = mock(UserRepositoryContract::class);
        $hasherMock = mock(Hasher::class);
        $service = new UserAuthenticationService($hasherMock, $userRepositoryMock);

        $service->retrieveByCredentials([]);
    }

    public function testRetrieveByCredentialsEmptyEmailFails()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('No valid identifying credential.');
        $userRepositoryMock = mock(UserRepositoryContract::class);
        $hasherMock = mock(Hasher::class);
        $service = new UserAuthenticationService($hasherMock, $userRepositoryMock);

        $service->retrieveByCredentials(['email' => '']);
    }
}