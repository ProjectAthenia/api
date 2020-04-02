<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Wiki;

use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticleVersionPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleVersionPolicyTest
 * @package Tests\Integration\Policies\Wiki
 */
class ArticleVersionPolicyTest extends TestCase
{
    use RolesTesting, DatabaseSetupTrait;

    public function IterationPolicy()
    {
        $policy = new ArticleVersionPolicy();

        foreach ([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER] as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertTrue($policy->all($user));
        }
    }

    public function testAllBlocks()
    {
        $policy = new ArticleVersionPolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->all($user));
        }
    }

    public function testCreateBlock()
    {
        $policy = new ArticleVersionPolicy();

        $user = factory(User::class)->create();
        $article = factory(Article::class)->create();

        $this->assertFalse($policy->create($user, $article));
    }

    public function testCreatePasses()
    {
        $policy = new ArticleVersionPolicy();

        $user = factory(User::class)->create();
        $article = factory(Article::class)->create([
            'created_by_id' => $user->id,
        ]);

        $this->assertTrue($policy->create($user, $article));
    }
}