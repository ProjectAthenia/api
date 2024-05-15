<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Feature;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Feature;
use App\Policies\FeaturePolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Feature
 */
class ViewRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return FeaturePolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Feature::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('feature'),
        ];
    }
}
