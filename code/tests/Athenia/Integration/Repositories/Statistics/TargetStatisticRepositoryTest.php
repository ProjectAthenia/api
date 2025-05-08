<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistics;

use App\Models\Statistics\TargetStatistic;
use App\Models\Statistics\Statistic;
use App\Models\Collection\Collection;
use App\Athenia\Repositories\Statistics\TargetStatisticRepository;
use App\Athenia\Events\Statistics\TargetStatisticCreatedEvent;
use App\Athenia\Events\Statistics\TargetStatisticUpdatedEvent;
use App\Athenia\Events\Statistics\TargetStatisticDeletedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class TargetStatisticRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Statistics
 */
class TargetStatisticRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var TargetStatisticRepository
     */
    protected $repository;

    /**
     * @var Dispatcher|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = mock(Dispatcher::class);
        $this->repository = new TargetStatisticRepository(
            new TargetStatistic(),
            $this->getGenericLogMock(),
            $this->dispatcher
        );
    }

    public function testFindAllReturnsCollection()
    {
        foreach (TargetStatistic::all() as $model) {
            $model->delete();
        }
        TargetStatistic::factory()->count(5)->create();

        $models = $this->repository->findAll();

        $this->assertCount(5, $models);
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $models);
    }

    public function testFindAllWithFilterReturnsCollection()
    {
        foreach (TargetStatistic::all() as $model) {
            $model->delete();
        }
        TargetStatistic::factory()->create(['id' => 1]);
        TargetStatistic::factory()->count(4)->create();

        $models = $this->repository->findAll(['id' => 1]);

        $this->assertCount(1, $models);
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $models);
    }

    public function testFindReturnsModel()
    {
        foreach (TargetStatistic::all() as $model) {
            $model->delete();
        }
        $model = TargetStatistic::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);

        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailThrowsException()
    {
        foreach (TargetStatistic::all() as $model) {
            $model->delete();
        }
        TargetStatistic::factory()->create(['id' => 35]);

        $this->expectException(\Exception::class);

        $this->repository->findOrFail(1);
    }

    public function testCreateForTargetSuccess()
    {
        $collection = Collection::factory()->create();
        $statistic = Statistic::factory()->create();

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof TargetStatisticCreatedEvent;
            }));

        $targetStatistic = $this->repository->createForTarget($collection, [
            'statistic_id' => $statistic->id,
            'value' => 42.5,
            'filters' => ['type' => 'test'],
        ]);

        $this->assertInstanceOf(TargetStatistic::class, $targetStatistic);
        $this->assertEquals($collection->id, $targetStatistic->target_id);
        $this->assertEquals('collection', $targetStatistic->target_type);
        $this->assertEquals($statistic->id, $targetStatistic->statistic_id);
        $this->assertEquals(42.5, $targetStatistic->value);
        $this->assertEquals(['type' => 'test'], $targetStatistic->filters);
    }

    public function testFindAllForTargetSuccess()
    {
        $collection = Collection::factory()->create();
        TargetStatistic::factory()->count(3)->create([
            'target_id' => $collection->id,
            'target_type' => 'collection',
        ]);
        // Create some stats for another collection to ensure filtering works
        TargetStatistic::factory()->count(2)->create();

        $results = $this->repository->findAllForTarget($collection);

        $this->assertCount(3, $results);
        $this->assertInstanceOf(EloquentCollection::class, $results);
        foreach ($results as $stat) {
            $this->assertEquals($collection->id, $stat->target_id);
            $this->assertEquals('collection', $stat->target_type);
        }
    }

    public function testFindForTargetSuccess()
    {
        $collection = Collection::factory()->create();
        $statistic = Statistic::factory()->create();
        TargetStatistic::factory()->create([
            'target_id' => $collection->id,
            'target_type' => 'collection',
            'statistic_id' => $statistic->id,
        ]);

        $result = $this->repository->findForTarget($collection, $statistic->id);

        $this->assertNotNull($result);
        $this->assertInstanceOf(TargetStatistic::class, $result);
        $this->assertEquals($collection->id, $result->target_id);
        $this->assertEquals($statistic->id, $result->statistic_id);
    }

    public function testFindForTargetReturnsNullWhenNotFound()
    {
        $collection = Collection::factory()->create();
        $result = $this->repository->findForTarget($collection, 999);

        $this->assertNull($result);
    }

    public function testUpdateSuccess()
    {
        $targetStatistic = TargetStatistic::factory()->create([
            'value' => 10.0,
            'filters' => ['old' => 'value'],
        ]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof TargetStatisticUpdatedEvent;
            }));

        $updatedStatistic = $this->repository->update($targetStatistic, [
            'value' => 20.0,
            'filters' => ['new' => 'value'],
        ]);

        $this->assertInstanceOf(TargetStatistic::class, $updatedStatistic);
        $this->assertEquals(20.0, $updatedStatistic->value);
        $this->assertEquals(['new' => 'value'], $updatedStatistic->filters);
    }

    public function testDeleteSuccess()
    {
        $targetStatistic = TargetStatistic::factory()->create();

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof TargetStatisticDeletedEvent;
            }));

        $this->repository->delete($targetStatistic);

        $this->assertNull(TargetStatistic::find($targetStatistic->id));
    }
} 