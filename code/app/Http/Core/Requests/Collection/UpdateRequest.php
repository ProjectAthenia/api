<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Collection;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Category;
use App\Models\Collection\Collection;
use App\Policies\CategoryPolicy;
use App\Policies\Collection\CollectionPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\Category
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
        return CollectionPolicy::ACTION_UPDATE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Collection $collection
     * @return array
     */
    public function rules(Collection $collection)
    {
        return $collection->getValidationRules(Collection::VALIDATION_RULES_UPDATE);
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