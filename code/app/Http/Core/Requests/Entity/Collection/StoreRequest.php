<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Entity\Collection;

use App\Contracts\Http\HasEntityInRequestContract;
use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Collection\Collection;
use App\Policies\Collection\CollectionPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\User\Thread
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
        return CollectionPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Collection::class;
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
     * The rules for the request
     *
     * @param Collection $collection
     * @return array
     */
    public function rules(Collection $collection)
    {
        return $collection->getValidationRules(Collection::VALIDATION_RULES_CREATE);
    }
}