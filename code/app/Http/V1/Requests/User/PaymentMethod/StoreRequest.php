<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\User\PaymentMethod;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Models\Payment\PaymentMethod;
use App\Policies\Payment\PaymentMethodPolicy;

/**
 * Class StoreRequest
 * @package App\Http\V1\Requests\User\PaymentMethod
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return PaymentMethodPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return PaymentMethod::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('user'),
        ];
    }

    /**
     * Get validation rules for the create request
     *
     * @param PaymentMethod $paymentMethod
     * @return array
     */
    public function rules(PaymentMethod $paymentMethod) : array
    {
        return $paymentMethod->getValidationRules(PaymentMethod::VALIDATION_RULES_CREATE);
    }
}