<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Article;

use App\Models\Category;
use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleUpdateTest
 * @package Tests\Athenia\Feature\Http\Article
 */
final class ArticleUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/articles';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('PUT', $this->path . '/' . $article->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $this->actAs($role);

            $article = Article::factory()->create([
                'created_by_id' => $this->actingAs->id,
            ]);
            $response = $this->json('PUT', $this->path . '/' . $article->id);

            $response->assertStatus(403);
        }
    }

    public function testNotFound(): void
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/1');

        $response->assertStatus(404);
    }

    public function testUpdateSuccessful(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'title' => 'A title',
            'created_by_id' => $this->actingAs->id,
        ]);

        $data = [
            'title' => 'A different title',
        ];

        $response = $this->json('PUT', $this->path . '/' . $article->id, $data);

        $response->assertStatus(200);
        $response->assertJson($data);

        /** @var Article $updated */
        $updated = Article::find($article->id);

        $this->assertEquals('A different title', $updated->title);
    }

    public function testUpdateSuccessfulWithCategories(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $category3 = Category::factory()->create();

        // Attach initial categories
        $article->categories()->attach([
            $category1->id => ['relevance' => 0.9],
            $category2->id => ['relevance' => 0.7],
        ]);

        // Update to different categories
        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => [
                ['category_id' => $category2->id, 'relevance' => 0.6],
                ['category_id' => $category3->id, 'relevance' => 0.4],
            ],
        ]);

        $response->assertStatus(200);

        /** @var Article $updated */
        $updated = Article::find($article->id);

        // Should have 2 categories now (category1 removed, category3 added)
        $this->assertCount(2, $updated->categories);

        // category1 should be removed
        $this->assertNull($updated->categories->find($category1->id));

        // category2 should have updated relevance
        $category2Pivot = $updated->categories->find($category2->id)->pivot;
        $this->assertEquals(0.6, $category2Pivot->relevance);

        // category3 should be newly added
        $category3Pivot = $updated->categories->find($category3->id)->pivot;
        $this->assertEquals(0.4, $category3Pivot->relevance);
    }

    public function testUpdateSuccessfulWithCategoriesDefaultRelevance(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $category = Category::factory()->create();

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => [
                ['category_id' => $category->id],
            ],
        ]);

        $response->assertStatus(200);

        /** @var Article $updated */
        $updated = Article::find($article->id);

        $this->assertCount(1, $updated->categories);

        $categoryPivot = $updated->categories->find($category->id)->pivot;
        $this->assertEquals(1.0, $categoryPivot->relevance);
    }

    public function testUpdateBlockedUserHasNotCreatedArticle(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);
        
        $article = Article::factory()->create();
        $response = $this->json('PUT', $this->path . '/' . $article->id);

        $response->assertStatus(403);
    }

    public function testUpdateFailsInvalidStringFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'title' => 1,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsStringsTooLong(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'title' => str_repeat('a', 121),
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title may not be greater than 120 characters.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidArrayFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => 'not-an-array',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'categories' => ['The categories must be an array.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidIntegerFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => [
                ['category_id' => 'not-an-integer'],
            ],
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'categories.0.category_id' => ['The categories.0.category_id must be an integer.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidNumericFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => [
                ['category_id' => 1, 'relevance' => 'not-numeric'],
            ],
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'categories.0.relevance' => ['The categories.0.relevance must be a number.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidModelFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $article = Article::factory()->create([
            'created_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PUT', $this->path . '/' . $article->id, [
            'categories' => [
                ['category_id' => 99999],
            ],
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'categories.0.category_id' => ['The selected categories.0.category_id is invalid.'],
            ]
        ]);
    }
}
