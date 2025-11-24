<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Authentication;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Validators\InvitationTokenIsValidValidator;
use App\Models\User\User;
use Illuminate\Contracts\Config\Repository;

/**
 * Class SignUpRequest
 * @package App\Http\Core\Requests\Authentication
 */
class SignUpRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * Gets the rules for the verification
     *
     * @param User $user
     * @param Repository $config
     * @return array
     */
    public function rules(User $user, Repository $config)
    {
        $rules = [
            'email' => 'required|string|max:120|email|unique:users,email',
            'first_name' => 'required|string|max:120',
            'last_name' => 'string|max:120',
            'password' => 'required|string|min:6|max:256',
        ];

        // Add invitation token validation if invitations are required
        if ($config->get('athenia.invitation_required', false)) {
            $rules['invitation_token'] = 'required|string|' . InvitationTokenIsValidValidator::KEY;
        }

        return $rules;
    }
}