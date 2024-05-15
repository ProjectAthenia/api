<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\Subscription;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Subscription\Subscription;
use App\Policies\Subscription\SubscriptionPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\Entity\Subscription
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoExpands, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return SubscriptionPolicy::ACTION_UPDATE;
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
            $this->route('subscription'),
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
        return $subscription->getValidationRules(Subscription::VALIDATION_RULES_UPDATE);
    }
}