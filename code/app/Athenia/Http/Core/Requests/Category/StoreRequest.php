<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Category;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Category;
use App\Policies\CategoryPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Category
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return CategoryPolicy::ACTION_CREATE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Category $category
     * @return array
     */
    public function rules(Category $category)
    {
        return $category->getValidationRules(Category::VALIDATION_RULES_CREATE);
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return Category::class;
    }
}