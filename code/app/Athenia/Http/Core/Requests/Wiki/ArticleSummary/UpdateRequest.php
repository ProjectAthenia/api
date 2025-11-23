<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Wiki\ArticleSummary;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Wiki\ArticleSummary;
use App\Policies\Wiki\ArticleSummaryPolicy;

/**
 * Class UpdateRequest
 * @package App\Athenia\Http\Core\Requests\Wiki\ArticleSummary
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticleSummaryPolicy::ACTION_UPDATE;
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

    /**
     * The rules for this request
     *
     * @param ArticleSummary $model
     */
    public function rules(ArticleSummary $model)
    {
        return $model->getValidationRules(ArticleSummary::VALIDATION_RULES_UPDATE);
    }
}
