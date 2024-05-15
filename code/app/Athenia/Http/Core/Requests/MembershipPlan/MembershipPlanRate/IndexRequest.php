<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\MembershipPlan\MembershipPlanRate;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Subscription\MembershipPlanRate;
use App\Policies\Subscription\MembershipPlanRatePolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\MembershipPlan\MembershipPlanRate
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return MembershipPlanRatePolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return MembershipPlanRate::class;
    }
}