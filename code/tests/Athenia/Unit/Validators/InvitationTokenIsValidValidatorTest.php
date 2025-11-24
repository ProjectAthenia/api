<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Validators;

use App\Athenia\Contracts\Repositories\User\InvitationTokenRepositoryContract;
use App\Athenia\Validators\InvitationTokenIsValidValidator;
use App\Models\User\InvitationToken;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class InvitationTokenIsValidValidatorTest
 * @package Tests\Athenia\Unit\Validators
 */
final class InvitationTokenIsValidValidatorTest extends TestCase
{
    public function testValidateReturnsTrueWithValidUnusedToken(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $invitationToken = new InvitationToken();
        $invitationToken->token = 'valid-token';
        $invitationToken->used_at = null;

        $repository->shouldReceive('findByToken')
            ->once()
            ->with('valid-token')
            ->andReturn($invitationToken);

        $validator = new InvitationTokenIsValidValidator($repository);

        $this->assertTrue($validator->validate('invitation_token', 'valid-token'));
    }

    public function testValidateReturnsFalseWhenTokenNotFound(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $repository->shouldReceive('findByToken')
            ->once()
            ->with('invalid-token')
            ->andReturn(null);

        $validator = new InvitationTokenIsValidValidator($repository);

        $this->assertFalse($validator->validate('invitation_token', 'invalid-token'));
    }

    public function testValidateReturnsFalseWhenTokenAlreadyUsed(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $invitationToken = new InvitationToken();
        $invitationToken->token = 'used-token';
        $invitationToken->used_at = now();

        $repository->shouldReceive('findByToken')
            ->once()
            ->with('used-token')
            ->andReturn($invitationToken);

        $validator = new InvitationTokenIsValidValidator($repository);

        $this->assertFalse($validator->validate('invitation_token', 'used-token'));
    }

    public function testValidateReturnsFalseWhenValueIsNotString(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $validator = new InvitationTokenIsValidValidator($repository);

        $this->assertFalse($validator->validate('invitation_token', 123));
    }

    public function testValidateReturnsFalseWhenValueIsArray(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $validator = new InvitationTokenIsValidValidator($repository);

        $this->assertFalse($validator->validate('invitation_token', ['invalid']));
    }
}
