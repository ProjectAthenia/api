<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\ArticleNote;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\User\ArticleNote;
use App\Policies\User\ArticleNotePolicy;

/**
 * Class RandomArticleRequest
 * @package App\Athenia\Http\Core\Requests\User\ArticleNote
 */
class RandomArticleRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands, HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticleNotePolicy::ACTION_CREATE;
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
        ];
    }
}
