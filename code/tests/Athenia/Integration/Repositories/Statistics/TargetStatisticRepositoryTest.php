<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistics;

use App\Models\Statistics\TargetStatistic;
use App\Models\Statistics\Statistic;
use App\Models\Collection\Collection;
use App\Athenia\Repositories\Statistics\TargetStatisticRepository;
use Illuminate\Contracts\Events\Dispatcher;
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

    public function testCreateForTargetSuccess()
    {
        $collection = Collection::factory()->create();
        $statistic = Statistic::factory()->create();

        $targetStatistic = $this->repository->createForTarget($collection, [
            'statistic_id' => $statistic->id,
            'value' => 42.5,
            'filters' => ['type' => 'test'],
        ]);

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
        $this->assertEquals($collection->id, $result->target_id);
        $this->assertEquals($statistic->id, $result->statistic_id);
    }

    public function testFindForTargetReturnsNullWhenNotFound()
    {
        $collection = Collection::factory()->create();
        $result = $this->repository->findForTarget($collection, 999);

        $this->assertNull($result);
    }
} 