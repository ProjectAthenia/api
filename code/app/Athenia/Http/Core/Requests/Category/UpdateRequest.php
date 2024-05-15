<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Category;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Category;
use App\Policies\CategoryPolicy;

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
        return CategoryPolicy::ACTION_UPDATE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Category $category
     * @return array
     */
    public function rules(Category $category)
    {
        return $category->getValidationRules(Category::VALIDATION_RULES_UPDATE);
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return Category::class;
    }

    /**
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('category'),
        ];
    }
}