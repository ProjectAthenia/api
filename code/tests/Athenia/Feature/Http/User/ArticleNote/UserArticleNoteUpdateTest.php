<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\User\ArticleNote;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteUpdateTest
 * @package Tests\Athenia\Feature\User\ArticleNote
 */
final class UserArticleNoteUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->user = User::factory()->create();

        $this->path.= $this->user->id . '/article-notes/';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $articleNote = ArticleNote::factory()->create();

        $response = $this->json('PUT', $this->path . $articleNote->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserBlocked(): void
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id);

        $response->assertStatus(403);
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('PUT', $this->path . '453');

        $response->assertStatus(404);
    }

    public function testUpdateSuccessful(): void
    {
        $this->actingAs($this->user);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
            'response' => 'Original response',
            'completed_at' => null,
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id, [
            'response' => 'Updated response',
        ]);

        $response->assertStatus(200);

        /** @var ArticleNote $updated */
        $updated = ArticleNote::find($articleNote->id);

        $this->assertEquals('Updated response', $updated->response);
    }

    public function testUpdateSuccessfulMarkCompleted(): void
    {
        $this->actingAs($this->user);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
            'completed_at' => null,
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id, [
            'completed' => true,
        ]);

        $response->assertStatus(200);

        /** @var ArticleNote $updated */
        $updated = ArticleNote::find($articleNote->id);

        $this->assertNotNull($updated->completed_at);
    }

    public function testUpdateSuccessfulUnmarkCompleted(): void
    {
        $this->actingAs($this->user);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
            'completed_at' => now(),
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id, [
            'completed' => false,
        ]);

        $response->assertStatus(200);

        /** @var ArticleNote $updated */
        $updated = ArticleNote::find($articleNote->id);

        $this->assertNull($updated->completed_at);
    }

    public function testUpdateFailsInvalidBooleanFields(): void
    {
        $this->actingAs($this->user);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id, [
            'completed' => 'not-a-boolean',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'completed' => ['The completed field must be true or false.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidStringFields(): void
    {
        $this->actingAs($this->user);

        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $articleNote->id, [
            'response' => 12345,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'response' => ['The response must be a string.'],
            ]
        ]);
    }
}
