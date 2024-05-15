<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Collection\CollectionItem;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Collection\CollectionItem;
use App\Policies\Collection\CollectionItemPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\User\Thread
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoRules, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return CollectionItemPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return CollectionItem::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('collection'),
        ];
    }

    /**
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'collectionItemCategories',
        ];
    }
}