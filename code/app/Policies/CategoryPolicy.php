<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User\User;

/**
 * Class CategoryPolicy
 * @package App\Policies
 */
class CategoryPolicy extends BasePolicyAbstract
{
    /**
     * All users can create a category
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Only super admins can update a category
     *
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        return false;
    }

    /**
     * Only super admins can delete a category
     *
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function delete(User $user, Category $category)
    {
        return false;
    }
}