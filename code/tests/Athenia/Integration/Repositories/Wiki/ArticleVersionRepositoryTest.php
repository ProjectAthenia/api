<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Wiki;

use App\Athenia\Events\Article\ArticleVersionCreatedEvent;
use App\Athenia\Repositories\Wiki\ArticleVersionRepository;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Models\Wiki\ArticleVersion;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleVersionRepositoryTest
 * @package Tests\Integration\Repositories\Wiki
 */
final class ArticleVersionRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleVersionRepository
     */
    protected $repository;

    /**
     * @var Dispatcher|CustomMockInterface
     */
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = mock(Dispatcher::class);

        $this->repository = new ArticleVersionRepository(
            new ArticleVersion(),
            $this->getGenericLogMock(),
            $this->dispatcher,
        );
    }

    public function testFindAllSuccess(): void
    {
        ArticleVersion::factory()->count(5)->create();
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
        $model = ArticleVersion::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        ArticleVersion::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $article = Article::factory()->create();
        $iteration = ArticleIteration::factory()->create();

        $this->dispatcher->shouldReceive('dispatch')->once()->with(\Mockery::on(function (ArticleVersionCreatedEvent $event) {
            return true;
        }));

        /** @var ArticleVersion $articleVersion */
        $articleVersion = $this->repository->create([
            'article_iteration_id' => $iteration->id,
        ], $article);

        $this->assertEquals($articleVersion->article_id, $article->id);
        $this->assertEquals($articleVersion->article_iteration_id, $iteration->id);
    }

    public function testUpdateSuccess(): void
    {
        $model = ArticleVersion::factory()->create([
            'name' => null,
        ]);
        $this->repository->update($model, [
            'name' => '1.0.0',
        ]);

        $updated = ArticleVersion::find($model->id);
        $this->assertEquals('1.0.0', $updated->name);
    }

    public function testDeleteSuccess(): void
    {
        $model = ArticleVersion::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(ArticleVersion::find($model->id));
    }
}
