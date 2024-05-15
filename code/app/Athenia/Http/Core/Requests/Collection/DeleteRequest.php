<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Collection;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Collection\Collection;
use App\Policies\Collection\CollectionPolicy;

/**
 * Class DeleteRequest
 * @package App\Http\Core\Requests\Category
 */
class DeleteRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return CollectionPolicy::ACTION_DELETE;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return Collection::class;
    }

    /**
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('collection'),
        ];
    }
}