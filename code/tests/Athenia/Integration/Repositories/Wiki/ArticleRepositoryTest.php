<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Wiki;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\Wiki\ArticleRepository;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Wiki
 */
final class ArticleRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleRepository(new Article(), $this->getGenericLogMock());
    }

    public function testDeleteThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Article());
    }

    public function testFindAllSuccess(): void
    {
        Article::factory()->count(5)->create();
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
        $model = Article::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Article::factory()->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess(): void
    {
        $user = User::factory()->create();

        /** @var Article $article */
        $article = $this->repository->create([
            'title' => 'An Article',
            'created_by_id' => $user->id,
        ]);

        $this->assertEquals('An Article', $article->title);
        $this->assertEquals($user->id, $article->created_by_id);
    }

    public function testUpdateSuccess(): void
    {
        $model = Article::factory()->create([
            'title' => 'Ann Article'
        ]);
        $this->repository->update($model, [
            'title' => 'An Article',
        ]);

        $updated = Article::find($model->id);
        $this->assertEquals('An Article', $updated->title);
    }
}
