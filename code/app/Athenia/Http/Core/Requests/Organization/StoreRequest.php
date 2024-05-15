<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Organization;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Organization\Organization;
use App\Policies\Organization\OrganizationPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Organization
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return OrganizationPolicy::ACTION_CREATE;
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
     * @param Organization $organization
     * @return array
     */
    public function rules(Organization $organization)
    {
        return $organization->getValidationRules(Organization::VALIDATION_RULES_CREATE);
    }
}