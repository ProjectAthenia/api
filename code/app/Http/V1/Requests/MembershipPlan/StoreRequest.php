<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\MembershipPlan;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoPolicyParameters;
use App\Models\Subscription\MembershipPlan;
use App\Policies\Subscription\MembershipPlanPolicy;

/**
 * Class StoreRequest
 * @package App\Http\V1\Requests\MembershipPlan
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return MembershipPlanPolicy::ACTION_CREATE;
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
     * @param MembershipPlan $membershipPlan
     * @return array
     */
    public function rules(MembershipPlan $membershipPlan)
    {
        return $membershipPlan->getValidationRules(MembershipPlan::VALIDATION_RULES_CREATE);
    }
}