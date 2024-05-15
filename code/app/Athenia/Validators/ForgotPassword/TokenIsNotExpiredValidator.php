<?php
declare(strict_types=1);

namespace App\Athenia\Validators\ForgotPassword;

use App\Athenia\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Validators\BaseValidatorAbstract;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

/**
 * Class TokenIsNotExpiredValidator
 * @package App\Validators\ForgotPassword
 */
class TokenIsNotExpiredValidator extends BaseValidatorAbstract
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var PasswordTokenRepositoryContract
     */
    private $passwordTokenRepository;

    /**
     * TokenIsNotExpiredValidator constructor.
     * @param Request $request
     * @param UserRepositoryContract $userRepository
     * @param PasswordTokenRepositoryContract $passwordTokenRepository
     */
    public function __construct(Request $request, UserRepositoryContract $userRepository,
                                PasswordTokenRepositoryContract $passwordTokenRepository)
    {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->passwordTokenRepository = $passwordTokenRepository;
    }

    /**
     * Responds to 'token_is_not_expired', and must be attached to the token field
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $this->ensureValidatorAttribute('token', $attribute);

        $email = $this->request->input('email', null);

        if (!$email) {
            return false;
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        $passwordToken = $this->passwordTokenRepository->findForUser($user, $value);

        if (!$passwordToken) {
            return false;
        }

        return $passwordToken->created_at->greaterThan(Carbon::now()->subMinutes(20));
    }
}