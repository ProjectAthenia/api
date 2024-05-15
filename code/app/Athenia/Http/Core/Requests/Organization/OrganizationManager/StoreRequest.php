<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Organization\OrganizationManager;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Organization\OrganizationManager;
use App\Policies\Organization\OrganizationManagerPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Organization\OrganizationManager
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return OrganizationManagerPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return OrganizationManager::class;
    }

    /**
     * @param OrganizationManager $organizationManager
     * @return array
     */
    public function rules(OrganizationManager $organizationManager)
    {
        return $organizationManager->getValidationRules(OrganizationManager::VALIDATION_RULES_CREATE);
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('organization'),
        ];
    }
}