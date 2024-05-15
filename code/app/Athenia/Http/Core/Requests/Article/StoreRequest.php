<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Article;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticlePolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Article
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticlePolicy::ACTION_CREATE;
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
     * Get validation rules for the create request
     *
     * @param Article $article
     * @return array
     */
    public function rules(Article $article) : array
    {
        return $article->getValidationRules(Article::VALIDATION_RULES_CREATE);
    }
}