<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Article;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticlePolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\Article
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