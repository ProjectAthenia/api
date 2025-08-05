<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Wiki;

use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticlePolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class ArticlePolicyTest
 * @package Tests\Athenia\Integration\Policies\Wiki
 */
final class ArticlePolicyTest extends TestCase
{
    use DatabaseSetupTrait, RolesTesting;

    public function testAllSuccess(): void
    {
        $policy = new ArticlePolicy();

        $this->assertTrue($policy->all(new User()));
    }

    public function testViewSuccess(): void
    {
        $policy = new ArticlePolicy();

        $this->assertTrue($policy->view(new User(),  new Article()));
    }

    public function testCreateSuccess(): void
    {
        $policy = new ArticlePolicy();

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $this->assertTrue($policy->create($user));
    }

    public function testCreateBlocks(): void
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->create($user));
        }
    }

    public function testUpdateSuccess(): void
    {
        $policy = new ArticlePolicy();

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $article = new Article([
            'created_by_id' => $user->id,
        ]);

        $this->assertTrue($policy->update($user, $article));
    }

    public function testUpdateBlocks(): void
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $article = new Article([
                'created_by_id' => $user->id,
            ]);

            $this->assertFalse($policy->update($user, $article));
        }

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $article = new Article();
        $this->assertFalse($policy->update($user, $article));
    }
}