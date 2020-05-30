<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Entity\Subscription;

use App\Contracts\Http\HasEntityInRequestContract;
use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Subscription\Subscription;
use App\Policies\Subscription\SubscriptionPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Entity\Subscription
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoExpands, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return SubscriptionPolicy::ACTION_CREATE;
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
     * Get validation rules for the create request
     *
     * @param Subscription $subscription
     * @return array
     */
    public function rules(Subscription $subscription) : array
    {
        return $subscription->getValidationRules(Subscription::VALIDATION_RULES_CREATE);
    }
}