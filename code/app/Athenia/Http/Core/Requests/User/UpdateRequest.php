<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\User;
use App\Policies\User\UserPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\User
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return UserPolicy::ACTION_UPDATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return User::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [$this->route('user')];
    }

    /**
     * Gets the validation rules needed for this request
     *
     * @param User $user
     * @return array
     */
    public function rules(User $user): array
    {
        return $user->getValidationRules(User::VALIDATION_RULES_UPDATE, $this->route('user'));
    }
}