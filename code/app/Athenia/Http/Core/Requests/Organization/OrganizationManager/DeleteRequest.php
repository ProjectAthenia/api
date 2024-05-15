<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Organization\OrganizationManager;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Organization\OrganizationManager;
use App\Policies\Organization\OrganizationManagerPolicy;

/**
 * Class DeleteRequest
 * @package App\Http\Core\Requests\Organization\OrganizationManager
 */
class DeleteRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return OrganizationManagerPolicy::ACTION_DELETE;
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
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('organization'),
            $this->route('organization_manager'),
        ];
    }
}