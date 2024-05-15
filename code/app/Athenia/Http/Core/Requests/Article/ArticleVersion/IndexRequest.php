<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Article\ArticleVersion;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Wiki\ArticleVersion;
use App\Policies\Wiki\ArticleVersionPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Article\ArticleVersion
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
        return ArticleVersionPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ArticleVersion::class;
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'iteration',
            'iteration.createdBy',
        ];
    }
}