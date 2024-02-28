<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Article;

use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class ArticleCreateTest
 * @package Tests\Feature\Http\Article
 */
class ArticleCreateTest extends TestCase
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

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserRoleBlocked()
    {
        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $this->actAs($role);
            $response = $this->json('POST', $this->path);

            $response->assertStatus(403);
        }
    }

    public function testCreateSuccessful()
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

    public function testCreateFailsRequiredFieldsNotPresent()
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

    public function testCreateFailsInvalidStringFields()
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

    public function testCreateFailsStringsTooLong()
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
}