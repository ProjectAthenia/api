<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\Category;
use App\Models\User\User;
use App\Policies\CategoryPolicy;
use Tests\TestCase;

/**
 * Class CategoryPolicyTest
 * @package Tests\Integration\Policies
 */
class CategoryPolicyTest extends TestCase
{
    public function testCreate()
    {
        $policy = new CategoryPolicy();
        $this->assertTrue($policy->create(new User()));
    }

    public function testUpdate()
    {
        $policy = new CategoryPolicy();
        $this->assertFalse($policy->update(new User(), new Category()));
    }

    public function testDelete()
    {
        $policy = new CategoryPolicy();
        $this->assertFalse($policy->delete(new User(), new Category()));
    }
}