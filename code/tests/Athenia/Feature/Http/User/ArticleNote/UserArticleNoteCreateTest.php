<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\User\ArticleNote;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteCreateTest
 * @package Tests\Athenia\Feature\User\ArticleNote
 */
final class UserArticleNoteCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->user = User::factory()->create();

        $this->path.= $this->user->id . '/article-notes';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testDifferentUserBlocked(): void
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $this->actingAs($this->user);

        $article = Article::factory()->create();

        $response = $this->json('POST', $this->path, [
            'article_id' => $article->id,
            'response' => 'My note',
        ]);

        $response->assertStatus(201);

        /** @var ArticleNote $articleNote */
        $articleNote = ArticleNote::first();
        $this->assertEquals($this->user->id, $articleNote->user_id);
        $this->assertEquals($article->id, $articleNote->article_id);
        $this->assertEquals('My note', $articleNote->response);
        $this->assertNull($articleNote->completed_at);
    }

    public function testCreateSuccessfulWithCompleted(): void
    {
        $this->actingAs($this->user);

        $article = Article::factory()->create();

        $response = $this->json('POST', $this->path, [
            'article_id' => $article->id,
            'completed' => true,
        ]);

        $response->assertStatus(201);

        /** @var ArticleNote $articleNote */
        $articleNote = ArticleNote::first();
        $this->assertNotNull($articleNote->completed_at);
    }

    public function testCreateFailsMissingRequiredFields(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'article_id' => ['The article id field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidBooleanFields(): void
    {
        $this->actingAs($this->user);

        $article = Article::factory()->create();

        $response = $this->json('POST', $this->path, [
            'article_id' => $article->id,
            'completed' => 'not-a-boolean',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'completed' => ['The completed field must be true or false.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields(): void
    {
        $this->actingAs($this->user);

        $article = Article::factory()->create();

        $response = $this->json('POST', $this->path, [
            'article_id' => $article->id,
            'response' => 12345,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'response' => ['The response must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidIntegerFields(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'article_id' => 'not-an-integer',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'article_id' => ['The article id must be an integer.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidModelFields(): void
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'article_id' => 99999,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'article_id' => ['The selected article id is invalid.'],
            ]
        ]);
    }
}
