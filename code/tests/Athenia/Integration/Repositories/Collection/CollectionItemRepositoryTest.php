<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Collection;

use App\Athenia\Repositories\Collection\CollectionItemRepository;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Wiki\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

final class CollectionItemRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var CollectionItemRepository
     */
    protected CollectionItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new CollectionItemRepository(
            new CollectionItem(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess(): void
    {
        CollectionItem::factory()->count(5)->create();
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
        $model = CollectionItem::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        CollectionItem::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $collection = Collection::factory()->create();
        $item = Article::factory()->create();

        /** @var CollectionItem $model */
        $model = $this->repository->create([
            'item_id' => $item->id,
            'item_type' => 'article',
                'order' => 4,
        ], $collection);

        $this->assertEquals('article', $model->item_type);
        $this->assertEquals($item->id, $model->item_id);
        $this->assertEquals(4, $model->order);
    }

    public function testUpdateSuccess(): void
    {
        $model = CollectionItem::factory()->create([
            'item_type' => 'release',
        ]);
        $this->repository->update($model, [
            'item_type' => 'game',
        ]);

        $updated = CollectionItem::find($model->id);
        $this->assertEquals('game', $updated->item_type);
    }

    public function testDeleteSuccess(): void
    {
        $model = CollectionItem::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(CollectionItem::find($model->id));
    }
}