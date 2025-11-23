<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Wiki;

use App\Models\Role;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticleSummaryPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ArticleSummaryPolicyTest
 * @package Tests\Athenia\Integration\Policies\Wiki
 */
final class ArticleSummaryPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testViewPasses(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertTrue($policy->view($user, $article));
    }

    public function testCreatePassesWithRole(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertTrue($policy->create($user, $article));
    }

    public function testCreateFailsWithoutRole(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertFalse($policy->create($user, $article));
    }

    public function testUpdatePassesWithRole(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertTrue($policy->update($user, $article));
    }

    public function testUpdateFailsWithoutRole(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertFalse($policy->update($user, $article));
    }

    public function testDeletePassesWithRole(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::ARTICLE_EDITOR);
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertTrue($policy->delete($user, $article));
    }

    public function testDeleteFailsWithoutRole(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $policy = new ArticleSummaryPolicy();

        $this->assertFalse($policy->delete($user, $article));
    }
}
