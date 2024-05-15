<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Authentication;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;

/**
 * Class LoginRequest
 * @package App\Http\Core\Requests\Authentication
 */
class LoginRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email' => 'required|max:256|email',
            'password' => 'required|max:256',
        ];
    }
}
