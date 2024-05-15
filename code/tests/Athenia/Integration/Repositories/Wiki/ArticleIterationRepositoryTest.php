<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Wiki;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Repositories\Wiki\ArticleIterationRepository;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleIterationRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Wiki
 */
final class ArticleIterationRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleIterationRepository
     */
    private ArticleIterationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleIterationRepository(
            new ArticleIteration(),
            $this->getGenericLogMock(),
        );
    }

    public function testDeleteThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new ArticleIteration());
    }

    public function testUpdateThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->update(new ArticleIteration(), []);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = ArticleIteration::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        ArticleIteration::factory()->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testFindAllSuccess(): void
    {
        ArticleIteration::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testCreateSuccess(): void
    {
        $article = Article::factory()->create();
        $user = User::factory()->create();

        /** @var ArticleIteration $model */
        $model = $this->repository->create([
            'content' => 'hello',
            'created_by_id' => $user->id,
        ], $article);

        $this->assertEquals('hello', $model->content);
        $this->assertEquals($article->id, $model->article_id);
        $this->assertEquals($user->id, $model->created_by_id);
    }
}
