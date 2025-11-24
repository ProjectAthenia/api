<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Wiki;

use App\Athenia\Repositories\Wiki\ArticleSummaryRepository;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleSummary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleSummaryRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Wiki
 */
final class ArticleSummaryRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleSummaryRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleSummaryRepository(
            new ArticleSummary(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess(): void
    {
        ArticleSummary::factory()->count(5)->create();
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
        $model = ArticleSummary::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        ArticleSummary::factory()->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess(): void
    {
        $article = Article::factory()->create();

        /** @var ArticleSummary $summary */
        $summary = $this->repository->create([
            'article_id' => $article->id,
            'content' => 'This is a test summary.',
        ]);

        $this->assertEquals('This is a test summary.', $summary->content);
        $this->assertEquals($article->id, $summary->article_id);
    }

    public function testUpdateSuccess(): void
    {
        $model = ArticleSummary::factory()->create([
            'content' => 'Original summary'
        ]);
        $this->repository->update($model, [
            'content' => 'Updated summary',
        ]);

        $updated = ArticleSummary::find($model->id);
        $this->assertEquals('Updated summary', $updated->content);
    }

    public function testDeleteSuccess(): void
    {
        $model = ArticleSummary::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(ArticleSummary::find($model->id));
    }
}
