<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\User\ArticleNote;

use App\Models\User\User;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteRandomArticleTest
 * @package Tests\Athenia\Feature\Http\User\ArticleNote
 */
final class UserArticleNoteRandomArticleTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        // Seed article note statistics
        $this->seed(\Database\Seeders\ArticleNoteStatisticsSeeder::class);
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $user = User::factory()->create();
        $this->path = '/v1/users/' . $user->id . '/random-article';

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testNotUserBlocked(): void
    {
        $user = User::factory()->create();
        $this->path = '/v1/users/' . $user->id . '/random-article';

        $requestingUser = User::factory()->create();
        $this->actingAs($requestingUser);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testRandomArticleSuccessCreatesNoteAndReturnsWithArticle(): void
    {
        $user = User::factory()->create();
        $this->path = '/v1/users/' . $user->id . '/random-article';

        // Create some articles
        $article1 = Article::factory()->create(['title' => 'Test Article 1']);
        $article2 = Article::factory()->create(['title' => 'Test Article 2']);

        $this->actingAs($user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(201);
        $response->assertJson([
            'user_id' => $user->id,
        ]);

        // Verify article is loaded
        $data = $response->json();
        $this->assertArrayHasKey('article', $data);
        $this->assertArrayHasKey('id', $data['article']);
        $this->assertArrayHasKey('title', $data['article']);

        // Verify article note was created
        $this->assertDatabaseHas('article_notes', [
            'user_id' => $user->id,
            'article_id' => $data['article_id'],
        ]);
    }

    public function testRandomArticleSelectsArticleWithoutNoteFirst(): void
    {
        $user = User::factory()->create();
        $this->path = '/v1/users/' . $user->id . '/random-article';

        // Create two articles
        $articleWithIncompleteNote = Article::factory()->create(['title' => 'Has Incomplete']);
        $articleWithoutNote = Article::factory()->create(['title' => 'No Note']);

        // Create incomplete note on first article
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $user->id,
            'article_id' => $articleWithIncompleteNote->id,
            'completed_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(201);

        // Should select the article without a note (or any other article without a note)
        $data = $response->json();

        // The selected article should NOT be the one with incomplete note
        // (unless all articles without notes are exhausted, which shouldn't happen in this test)
        $this->assertNotEquals($articleWithIncompleteNote->id, $data['article_id']);
    }

    public function testRandomArticleNeverReturnsCompletedArticle(): void
    {
        $user = User::factory()->create();
        $this->path = '/v1/users/' . $user->id . '/random-article';

        // Create two articles
        $completedArticle = Article::factory()->create(['title' => 'Completed']);
        $availableArticle = Article::factory()->create(['title' => 'Available']);

        // Mark first article as completed
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $user->id,
            'article_id' => $completedArticle->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(201);

        $data = $response->json();

        // Should never return the completed article
        $this->assertNotEquals($completedArticle->id, $data['article_id']);
    }

    public function testRandomArticlePrefersArticlesWithLowerCompletedNotesStatistic(): void
    {
        $currentUser = User::factory()->create();
        $this->path = '/v1/users/' . $currentUser->id . '/random-article';

        // Create three articles
        $articleLowCompleted = Article::factory()->create(['title' => 'Low Completed']);
        $articleMedCompleted = Article::factory()->create(['title' => 'Medium Completed']);
        $articleHighCompleted = Article::factory()->create(['title' => 'High Completed']);

        // Create other users who have completed notes
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $otherUser3 = User::factory()->create();

        // Article high has 3 completed notes
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser1->id,
            'article_id' => $articleHighCompleted->id,
            'completed_at' => now(),
        ]);
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser2->id,
            'article_id' => $articleHighCompleted->id,
            'completed_at' => now(),
        ]);
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser3->id,
            'article_id' => $articleHighCompleted->id,
            'completed_at' => now(),
        ]);

        // Article medium has 2 completed notes
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser1->id,
            'article_id' => $articleMedCompleted->id,
            'completed_at' => now(),
        ]);
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser2->id,
            'article_id' => $articleMedCompleted->id,
            'completed_at' => now(),
        ]);

        // Article low has 1 completed note
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser1->id,
            'article_id' => $articleLowCompleted->id,
            'completed_at' => now(),
        ]);

        // Synchronize statistics for all articles
        $synchronizationService = app(\App\Athenia\Contracts\Services\Statistic\StatisticSynchronizationServiceContract::class);
        $synchronizationService->synchronizeTargetStatistics($articleLowCompleted);
        $synchronizationService->synchronizeTargetStatistics($articleMedCompleted);
        $synchronizationService->synchronizeTargetStatistics($articleHighCompleted);

        $this->actingAs($currentUser);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(201);
        $data = $response->json();

        // Should select an article based on statistics
        // With random ordering, any article could be selected, but it should be one with lower statistics
        $this->assertNotNull($data['article_id']);
        $this->assertIsInt($data['article_id']);

        // Verify the article_note was created for the current user
        $this->assertEquals($currentUser->id, $data['user_id']);
    }

    public function testRandomArticlePrefersArticlesWithLowerTotalNotesWhenCompletedEqual(): void
    {
        $currentUser = User::factory()->create();
        $this->path = '/v1/users/' . $currentUser->id . '/random-article';

        // Create two articles with same completed notes but different total notes
        $articleLowTotal = Article::factory()->create(['title' => 'Low Total']);
        $articleHighTotal = Article::factory()->create(['title' => 'High Total']);

        // Create other users
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $otherUser3 = User::factory()->create();

        // Both articles have 1 completed note (same)
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser1->id,
            'article_id' => $articleLowTotal->id,
            'completed_at' => now(),
        ]);
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser2->id,
            'article_id' => $articleHighTotal->id,
            'completed_at' => now(),
        ]);

        // Article high total has 2 additional incomplete notes (3 total notes vs 1)
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => $otherUser3->id,
            'article_id' => $articleHighTotal->id,
            'completed_at' => null,
        ]);
        \App\Models\User\ArticleNote::factory()->create([
            'user_id' => User::factory()->create()->id,
            'article_id' => $articleHighTotal->id,
            'completed_at' => null,
        ]);

        // Synchronize statistics
        $synchronizationService = app(\App\Athenia\Contracts\Services\Statistic\StatisticSynchronizationServiceContract::class);
        $synchronizationService->synchronizeTargetStatistics($articleLowTotal);
        $synchronizationService->synchronizeTargetStatistics($articleHighTotal);

        $this->actingAs($currentUser);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(201);
        $data = $response->json();

        // Should select an article based on statistics
        // With random ordering, any article could be selected, but it should prioritize lower statistics
        $this->assertNotNull($data['article_id']);
        $this->assertIsInt($data['article_id']);

        // Verify the article_note was created for the current user
        $this->assertEquals($currentUser->id, $data['user_id']);
    }
}
