<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\User\ArticleNote;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteViewTest
 * @package Tests\Athenia\Feature\User\ArticleNote
 */
final class UserArticleNoteViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

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

        $response = $this->json('GET', $this->path . $user->id . '/article-notes/' . $articleNote->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserBlocked(): void
    {
        $this->actAsUser();
        $otherUser = User::factory()->create();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->json('GET', $this->path . $otherUser->id . '/article-notes/' . $articleNote->id);

        $response->assertStatus(403);
    }

    public function testUserNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '99999/article-notes/1');

        $response->assertStatus(404);
    }

    public function testArticleNoteNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes/99999');

        $response->assertStatus(404);
    }

    public function testGetSingleSuccess(): void
    {
        $this->actAsUser();
        $articleNote = ArticleNote::factory()->create([
            'user_id' => $this->actingAs->id,
            'response' => 'Test response',
        ]);

        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes/' . $articleNote->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $articleNote->id,
            'user_id' => $this->actingAs->id,
            'response' => 'Test response',
        ]);
    }
}
