<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\ArticleNote;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\ArticleNote;
use App\Policies\User\ArticleNotePolicy;

/**
 * Class UpdateRequest
 * @package App\Athenia\Http\Core\Requests\User\ArticleNote
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
        return ArticleNotePolicy::ACTION_UPDATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ArticleNote::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('user'),
            $this->route('article_note'),
        ];
    }

    /**
     * The rules for this request
     *
     * @param ArticleNote $model
     */
    public function rules(ArticleNote $model)
    {
        return $model->getValidationRules(ArticleNote::VALIDATION_RULES_UPDATE);
    }
}
