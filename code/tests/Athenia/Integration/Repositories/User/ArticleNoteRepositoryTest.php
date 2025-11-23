<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\User;

use App\Athenia\Repositories\User\ArticleNoteRepository;
use App\Models\User\ArticleNote;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleNoteRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\User
 */
final class ArticleNoteRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleNoteRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleNoteRepository(
            new ArticleNote(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess(): void
    {
        ArticleNote::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = ArticleNote::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        ArticleNote::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var ArticleNote $articleNote */
        $articleNote = $this->repository->create([
            'article_id' => $article->id,
            'response' => 'A response',
        ], $user);

        $this->assertEquals($articleNote->user_id, $user->id);
        $this->assertEquals($articleNote->article_id, $article->id);
        $this->assertEquals('A response', $articleNote->response);
        $this->assertNull($articleNote->completed_at);
    }

    public function testCreateSuccessWithCompleted(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Article $article */
        $article = Article::factory()->create();

        /** @var ArticleNote $articleNote */
        $articleNote = $this->repository->create([
            'article_id' => $article->id,
            'completed' => true,
        ], $user);

        $this->assertEquals($articleNote->user_id, $user->id);
        $this->assertEquals($articleNote->article_id, $article->id);
        $this->assertNotNull($articleNote->completed_at);
    }

    public function testUpdateSuccess(): void
    {
        $model = ArticleNote::factory()->create([
            'response' => 'Original response',
            'completed_at' => null,
        ]);

        $updated = $this->repository->update($model, [
            'response' => 'Updated response',
            'completed' => true,
        ]);

        $this->assertEquals('Updated response', $updated->response);
        $this->assertNotNull($updated->completed_at);
    }

    public function testUpdateSuccessUnmarkCompleted(): void
    {
        $model = ArticleNote::factory()->create([
            'completed_at' => now(),
        ]);

        $updated = $this->repository->update($model, [
            'completed' => false,
        ]);

        $this->assertNull($updated->completed_at);
    }

    public function testDeleteSuccess(): void
    {
        $model = ArticleNote::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(ArticleNote::find($model->id));
    }
}
