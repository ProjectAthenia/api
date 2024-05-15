<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\MembershipPlan;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Subscription\MembershipPlan;
use App\Policies\Subscription\MembershipPlanPolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\MembershipPlan
 */
class RetrieveRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return MembershipPlanPolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return MembershipPlan::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('membership_plan'),
        ];
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'features',
        ];
    }
}
