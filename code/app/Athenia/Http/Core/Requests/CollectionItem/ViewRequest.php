<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\CollectionItem;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Collection\CollectionItem;
use App\Policies\Collection\CollectionItemPolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Category
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
        return CollectionItemPolicy::ACTION_VIEW;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return CollectionItem::class;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('collection_item')->collection,
            $this->route('collection_item'),
        ];
    }
}
