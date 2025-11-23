<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\User\ArticleNote;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteDeleteTest
 * @package Tests\Athenia\Feature\User\ArticleNote
 */
final class UserArticleNoteDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $user = User::factory()->create();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('DELETE', '/v1/users/' . $user->id . '/article-notes/' . $articleNote->id);
        $response->assertStatus(403);
    }

    public function testDifferentUserBlocked(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('DELETE', '/v1/users/' . $user->id . '/article-notes/' . $articleNote->id);
        $response->assertStatus(403);
    }

    public function testDeleteSingle(): void
    {
        $this->actAsUser();

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->actingAs->id,
        ]);

        $response = $this->json('DELETE', '/v1/users/' . $this->actingAs->id . '/article-notes/' . $articleNote->id);

        $response->assertStatus(204);
        $this->assertNull(ArticleNote::find($articleNote->id));
    }

    public function testDeleteSingleInvalidIdFails(): void
    {
        $this->actAsUser();

        $response = $this->json('DELETE', '/v1/users/' . $this->actingAs->id . '/article-notes/a')
            ->assertExactJson([
                'message'   => 'This item was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails(): void
    {
        $this->actAsUser();

        $response = $this->json('DELETE', '/v1/users/' . $this->actingAs->id . '/article-notes/99999')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}
