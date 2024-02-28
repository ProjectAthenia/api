<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Wiki;

use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleModification;
use App\Repositories\Wiki\ArticleModificationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleModificationRepositoryTest
 * @package Tests\Integration\Repositories\Wiki
 */
class ArticleModificationRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleModificationRepository
     */
    protected ArticleModificationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleModificationRepository(
            new ArticleModification(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess(): void
    {
        ArticleModification::factory()->count(5)->create();
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
        $model = ArticleModification::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        ArticleModification::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $article = Article::factory()->create();

        /** @var ArticleModification $articleModification */
        $articleModification = $this->repository->create([
            'content' => 'Some Content',
            'action' => 'add',
            'start_position' => 0,
        ], $article);

        $this->assertEquals($articleModification->article_id, $article->id);
        $this->assertEquals($articleModification->content, 'Some Content');
        $this->assertEquals($articleModification->action, 'add');
        $this->assertEquals($articleModification->start_position, 0);
    }

    public function testUpdateSuccess(): void
    {
        $model = ArticleModification::factory()->create([
            'start_position' => 0,
        ]);
        $this->repository->update($model, [
            'start_position' => 10,
        ]);

        $updated = ArticleModification::find($model->id);
        $this->assertEquals($updated->start_position, 10);
    }

    public function testDeleteSuccess(): void
    {
        $model = ArticleModification::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(ArticleModification::find($model->id));
    }
}
