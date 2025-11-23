<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Wiki\ArticleSummary;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Wiki\ArticleSummary;
use App\Policies\Wiki\ArticleSummaryPolicy;

/**
 * Class ViewRequest
 * @package App\Athenia\Http\Core\Requests\Wiki\ArticleSummary
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
        return ArticleSummaryPolicy::ACTION_VIEW;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return ArticleSummary::class;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('article'),
        ];
    }
}
