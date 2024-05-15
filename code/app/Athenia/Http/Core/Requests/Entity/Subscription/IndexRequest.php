<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\Subscription;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Subscription\Subscription;
use App\Policies\Subscription\SubscriptionPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Entity\Subscription
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoRules, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return SubscriptionPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Subscription::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->getEntity(),
        ];
    }

    /**
     * @return array|string[]
     */
    public function allowedExpands(): array
    {
        return [
            'membershipPlanRate',
            'membershipPlanRate.membershipPlan',
        ];
    }
}