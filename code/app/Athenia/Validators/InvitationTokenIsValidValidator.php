<?php
declare(strict_types=1);

namespace App\Athenia\Validators;

use App\Athenia\Contracts\Repositories\User\InvitationTokenRepositoryContract;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class InvitationTokenIsValidValidator
 * @package App\Athenia\Validators
 */
class InvitationTokenIsValidValidator
{
    /**
     * The key for easy reference around the app
     */
    const KEY = 'invitation_token_is_valid';

    /**
     * @var InvitationTokenRepositoryContract
     */
    private InvitationTokenRepositoryContract $invitationTokenRepository;

    /**
     * InvitationTokenIsValidValidator constructor.
     * @param InvitationTokenRepositoryContract $invitationTokenRepository
     */
    public function __construct(InvitationTokenRepositoryContract $invitationTokenRepository)
    {
        $this->invitationTokenRepository = $invitationTokenRepository;
    }

    /**
     * This is invoked by the validator rule 'invitation_token_is_valid'
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $invitationToken = $this->invitationTokenRepository->findByToken($value);

        if (!$invitationToken) {
            return false;
        }

        if ($invitationToken->isUsed()) {
            return false;
        }

        return true;
    }
}
