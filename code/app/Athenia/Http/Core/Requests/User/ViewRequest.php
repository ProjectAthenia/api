<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\User\User;
use App\Policies\User\UserPolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\User
 */
class ViewRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return UserPolicy::ACTION_VIEW;
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
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'roles',
        ];
    }
}