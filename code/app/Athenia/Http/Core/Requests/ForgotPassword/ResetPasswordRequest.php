<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\ForgotPassword;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use Illuminate\Validation\Rule;

/**
 * Class ResetPasswordRequest
 * @package App\Http\Core\Requests\ForgotPassword
 */
class ResetPasswordRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * get the validation rules for resetting a password
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email' => 'required|max:120|email|' . Rule::exists('users', 'email'),
            'token' => 'required|max:40|' .
                Rule::exists('password_tokens', 'token') .
                '|user_owns_token|token_is_not_expired',
            'password' => 'required|max:120',
        ];
    }
}