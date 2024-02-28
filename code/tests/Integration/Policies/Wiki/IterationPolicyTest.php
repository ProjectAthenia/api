<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Wiki;

use App\Models\Role;
use App\Policies\Wiki\ArticleIterationPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class IterationPolicyTest
 * @package Tests\Integration\Policies
 */
class IterationPolicyTest extends TestCase
{
    use DatabaseSetupTrait, RolesTesting;

    public function IterationPolicy()
    {
        $policy = new ArticleIterationPolicy();

        foreach ([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER] as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertTrue($policy->all($user));
        }
    }

    public function testAllBlocks(): void
    {
        $policy = new ArticleIterationPolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->all($user));
        }
    }
}
