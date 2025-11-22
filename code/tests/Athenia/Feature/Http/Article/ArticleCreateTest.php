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
 * Class ArticleCreateTest
 * @package Tests\Athenia\Feature\Http\Article
 */
final class ArticleCreateTest extends TestCase
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
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $this->actAs($role);
            $response = $this->json('POST', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testCreateSuccessful(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'title' => 'An Article',
            'created_by_id' => $this->actingAs->id,
            'content' => '',
        ]);
    }

    public function testCreateSuccessfulWithCategories(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
            'categories' => [
                ['category_id' => $category1->id, 'relevance' => 0.8],
                ['category_id' => $category2->id, 'relevance' => 0.5],
            ],
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'title' => 'An Article',
            'created_by_id' => $this->actingAs->id,
        ]);

        /** @var Article $article */
        $article = Article::find($response->json('id'));

        $this->assertCount(2, $article->categories);

        $category1Pivot = $article->categories->find($category1->id)->pivot;
        $this->assertEquals(0.8, $category1Pivot->relevance);

        $category2Pivot = $article->categories->find($category2->id)->pivot;
        $this->assertEquals(0.5, $category2Pivot->relevance);
    }

    public function testCreateSuccessfulWithCategoriesDefaultRelevance(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $category = Category::factory()->create();

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
            'categories' => [
                ['category_id' => $category->id],
            ],
        ]);

        $response->assertStatus(201);

        /** @var Article $article */
        $article = Article::find($response->json('id'));

        $this->assertCount(1, $article->categories);

        $categoryPivot = $article->categories->find($category->id)->pivot;
        $this->assertEquals(1.0, $categoryPivot->relevance);
    }

    public function testCreateFailsRequiredFieldsNotPresent(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
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

        $response = $this->json('POST', $this->path, [
            'title' => str_repeat('a', 121),
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'title' => ['The title may not be greater than 120 characters.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidArrayFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
            'categories' => 'not-an-array',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'categories' => ['The categories must be an array.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidIntegerFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
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

    public function testCreateFailsInvalidNumericFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
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

    public function testCreateFailsInvalidModelFields(): void
    {
        $this->actAs(Role::ARTICLE_EDITOR);

        $response = $this->json('POST', $this->path, [
            'title' => 'An Article',
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