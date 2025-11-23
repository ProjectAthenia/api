<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Wiki\ArticleSummary;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Wiki\ArticleSummary;
use App\Policies\Wiki\ArticleSummaryPolicy;

/**
 * Class DeleteRequest
 * @package App\Athenia\Http\Core\Requests\Wiki\ArticleSummary
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
        return ArticleSummaryPolicy::ACTION_DELETE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ArticleSummary::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('article'),
        ];
    }
}
