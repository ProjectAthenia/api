<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\User;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use App\Policies\User\ArticleNotePolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ArticleNotePolicyTest
 * @package Tests\Athenia\Integration\Policies\User
 */
final class ArticleNotePolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPasses(): void
    {
        $user = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $this->assertTrue($policy->all($user, $user));
    }

    public function testAllFails(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $this->assertFalse($policy->all($user1, $user2));
    }

    public function testCreatePasses(): void
    {
        $user = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $this->assertTrue($policy->create($user, $user));
    }

    public function testCreateFails(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $this->assertFalse($policy->create($user1, $user2));
    }

    public function testViewPasses(): void
    {
        $user = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->assertTrue($policy->view($user, $user, $articleNote));
    }

    public function testViewFails(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user2->id,
        ]);

        $policy = new ArticleNotePolicy();

        $this->assertFalse($policy->view($user1, $user2, $articleNote));
        $this->assertFalse($policy->view($user1, $user1, $articleNote));
    }

    public function testUpdatePasses(): void
    {
        $user = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->assertTrue($policy->update($user, $user, $articleNote));
    }

    public function testUpdateFails(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user2->id,
        ]);

        $policy = new ArticleNotePolicy();

        $this->assertFalse($policy->update($user1, $user2, $articleNote));
        $this->assertFalse($policy->update($user1, $user1, $articleNote));
    }

    public function testDeletePasses(): void
    {
        $user = User::factory()->create();

        $policy = new ArticleNotePolicy();

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->assertTrue($policy->delete($user, $user, $articleNote));
    }

    public function testDeleteFails(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user2->id,
        ]);

        $policy = new ArticleNotePolicy();

        $this->assertFalse($policy->delete($user1, $user2, $articleNote));
        $this->assertFalse($policy->delete($user1, $user1, $articleNote));
    }
}
