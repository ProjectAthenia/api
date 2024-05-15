<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\PaymentMethod;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Payment\PaymentMethod;
use App\Policies\Payment\PaymentMethodPolicy;

/**
 * Class DeleteRequest
 * @package App\Http\Core\Requests\Entity\PaymentMethod
 */
class DeleteRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoRules, HasNoExpands, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return PaymentMethodPolicy::ACTION_DELETE;
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
            $this->getEntity(),
            $this->route('payment_method')
        ];
    }
}