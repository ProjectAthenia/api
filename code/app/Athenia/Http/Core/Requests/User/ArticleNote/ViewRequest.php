<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\ArticleNote;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\User\ArticleNote;
use App\Policies\User\ArticleNotePolicy;

/**
 * Class ViewRequest
 * @package App\Athenia\Http\Core\Requests\User\ArticleNote
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
        return ArticleNotePolicy::ACTION_VIEW;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return ArticleNote::class;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('user'),
            $this->route('article_note'),
        ];
    }
}
