<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Article;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoRules;
use App\Models\Wiki\Article;

/**
 * Class DeleteRequest
 * @package App\Http\V1\Requests\Article
 */
class DeleteRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticlePolicy::ACTION_DELETE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Article::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [$this->route('article')];
    }
}