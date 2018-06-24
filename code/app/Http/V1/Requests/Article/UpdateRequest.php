<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Article;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Models\Wiki\Article;
use App\Policies\ArticlePolicy;

/**
 * Class UpdateRequest
 * @package App\Http\V1\Requests\Article
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract
{
    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticlePolicy::ACTION_UPDATE;
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

    /**
     * Gets the validation rules for this request
     *
     * @param Article $article
     * @return array
     */
    public function rules(Article $article): array
    {
        return $article->getValidationRules(Article::VALIDATION_RULES_UPDATE);
    }
}