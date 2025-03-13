<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Organization;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Organization\Organization;
use App\Policies\Organization\OrganizationPolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Organization
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
        return OrganizationPolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Organization::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('organization'),
        ];
    }

    /**
     * All allowed expands for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'paymentMethods',
            'subscriptions',
            'subscriptions.membershipPlanRate',
            'subscriptions.membershipPlanRate.membershipPlan',
            'subscriptions.membershipPlanRate.membershipPlan.features',
            'subscriptions.paymentMethod',
        ];
    }
}
