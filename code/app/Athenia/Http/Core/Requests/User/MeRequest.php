<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\User\User;
use App\Policies\User\UserPolicy;

/**
 * Class MeRequest
 * @package App\Http\Core\Requests\User
 */
class MeRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return UserPolicy::ACTION_VIEW_SELF;
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
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'organizationManagers',
            'organizationManagers.organization',
            'paymentMethods',
            'roles',
            'subscriptions',
            'subscriptions.membershipPlanRate',
            'subscriptions.membershipPlanRate.membershipPlan',
            'subscriptions.membershipPlanRate.membershipPlan.features',
            'subscriptions.paymentMethod',
        ];
    }
}
