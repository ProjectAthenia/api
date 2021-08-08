<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Article\Iteration;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Wiki\ArticleIteration;
use App\Policies\Wiki\IterationPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Article\Iteration
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return IterationPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ArticleIteration::class;
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'article',
            'createdBy',
        ];
    }
}
