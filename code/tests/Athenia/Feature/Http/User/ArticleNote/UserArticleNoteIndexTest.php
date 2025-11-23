<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\User\ArticleNote;

use App\Models\User\ArticleNote;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserArticleNoteIndexTest
 * @package Tests\Athenia\Feature\User\ArticleNote
 */
final class UserArticleNoteIndexTest extends TestCase
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
        User::unsetEventDispatcher();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $user = User::factory()->create();

        $response = $this->json('GET', $this->path . $user->id . '/article-notes');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked(): void
    {
        $this->actAsUser();
        $user = User::factory()->create();

        $response = $this->json('GET', $this->path . $user->id . '/article-notes');

        $response->assertStatus(403);
    }

    public function testUserNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/article-notes');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty(): void
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/article-notes');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult(): void
    {
        $this->actAsUser();

        ArticleNote::factory()->count(4)->create();
        ArticleNote::factory()->count(15)->create([
            'user_id' => $this->actingAs->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleNote())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes?page=2');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 10,
            'from' => 11,
            'to' => 15,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleNote())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes?page=2&limit=5');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
            'last_page' => 3
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new ArticleNote())->toArray())
                ]
            ]);
    }

    public function testGetPaginationWithExpands(): void
    {
        $this->actAsUser();

        ArticleNote::factory()->count(5)->create([
            'user_id' => $this->actingAs->id,
        ]);

        // with expands
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/article-notes?expand[user]=*&expand[article]=*');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 5,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 5,
            'last_page' => 1
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' => array_keys((new ArticleNote())->toArray())
                ]
            ]);
    }
}
