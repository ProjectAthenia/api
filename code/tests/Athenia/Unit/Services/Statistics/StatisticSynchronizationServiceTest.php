<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Services\Statistics\StatisticSynchronizationService;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasStatistics;

/**
 * Class StatisticSynchronizationServiceTest
 * @package Tests\Athenia\Unit\Services\Statistics
 */
class StatisticSynchronizationServiceTest extends TestCase
{
    /**
     * @var StatisticRepositoryContract|MockInterface
     */
    private MockInterface $statisticRepository;

    /**
     * @var TargetStatisticRepositoryContract|MockInterface
     */
    private MockInterface $targetStatisticRepository;

    /**
     * @var StatisticSynchronizationService
     */
    private StatisticSynchronizationService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->statisticRepository = Mockery::mock(StatisticRepositoryContract::class);
        $this->targetStatisticRepository = Mockery::mock(TargetStatisticRepositoryContract::class);
        $this->service = new StatisticSynchronizationService(
            $this->statisticRepository,
            $this->targetStatisticRepository
        );
    }

    public function testSynchronizeTargetStatisticsWithNoExistingTargets()
    {
        $modelClass = 'App\Models\TestModel';
        $modelId = 123;

        $statistic = new Statistic();
        $statistic->id = 456;

        $targetStatistic = new TargetStatistic();
        $targetStatistic->statistic_id = 456;
        $targetStatistic->target_id = $modelId;
        $targetStatistic->target_type = $modelClass;

        $model = new class extends Model implements CanBeStatisticTargetContract {
            use HasStatistics;
            public $id = 123;
            public $targetStatistics;
            public function morphRelationName(): string { return 'App\Models\TestModel'; }
        };
        $model->targetStatistics = new BaseCollection();

        $this->statisticRepository->shouldReceive('findAll')
            ->once()
            ->andReturn(new BaseCollection([$statistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->once()
            ->with([
                'statistic_id' => 456,
                'target_id' => 123,
                'target_type' => 'App\Models\TestModel',
            ])
            ->andReturn($targetStatistic);

        $result = $this->service->synchronizeTargetStatistics($model);

        $this->assertInstanceOf(BaseCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(456, $result->first()->statistic_id);
    }

    public function testSynchronizeTargetStatisticsWithExistingTargets()
    {
        $modelClass = 'App\Models\TestModel';
        $modelId = 123;

        /** @var Statistic|MockInterface $existingStatistic */
        $existingStatistic = Mockery::mock(Statistic::class);
        $existingStatistic->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(456);
        $existingStatistic->shouldReceive('setAttribute')
            ->withAnyArgs()
            ->andReturnSelf();

        /** @var Statistic|MockInterface $newStatistic */
        $newStatistic = Mockery::mock(Statistic::class);
        $newStatistic->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(789);
        $newStatistic->shouldReceive('setAttribute')
            ->withAnyArgs()
            ->andReturnSelf();

        /** @var TargetStatistic|MockInterface $existingTargetStatistic */
        $existingTargetStatistic = Mockery::mock(TargetStatistic::class);
        $existingTargetStatistic->shouldReceive('getAttribute')
            ->with('statistic_id')
            ->andReturn(456);
        $existingTargetStatistic->shouldReceive('setAttribute')
            ->withAnyArgs()
            ->andReturnSelf();
        $existingTargetStatistic->shouldReceive('offsetExists')
            ->withAnyArgs()
            ->andReturn(false);
        $existingTargetStatistic->shouldReceive('offsetGet')
            ->withAnyArgs()
            ->andReturn(null);
        $existingTargetStatistic->shouldReceive('offsetSet')
            ->withAnyArgs()
            ->andReturnSelf();

        /** @var TargetStatistic|MockInterface $newTargetStatistic */
        $newTargetStatistic = Mockery::mock(TargetStatistic::class);
        $newTargetStatistic->shouldReceive('offsetExists')
            ->withAnyArgs()
            ->andReturn(false);
        $newTargetStatistic->shouldReceive('offsetGet')
            ->withAnyArgs()
            ->andReturn(null);
        $newTargetStatistic->shouldReceive('offsetSet')
            ->withAnyArgs()
            ->andReturnSelf();

        /** @var Collection|MockInterface $existingTargetStatistics */
        $existingTargetStatistics = Mockery::mock(Collection::class);
        $existingTargetStatistics->shouldReceive('keyBy')
            ->with('statistic_id')
            ->andReturn(new BaseCollection([456 => $existingTargetStatistic]));
        $existingTargetStatistics->shouldReceive('concat')
            ->withAnyArgs()
            ->andReturn(new Collection([$existingTargetStatistic, $newTargetStatistic]));

        /** @var CanBeStatisticTargetContract|Model|MockInterface $model */
        $model = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $model->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($modelId);
        $model->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn($existingTargetStatistics);
        $model->shouldReceive('morphRelationName')
            ->andReturn($modelClass);

        $this->statisticRepository->shouldReceive('findWhere')
            ->with(['model' => $modelClass])
            ->andReturn(collect([$existingStatistic, $newStatistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->with([
                'statistic_id' => 789,
                'target_id' => $modelId,
                'target_type' => $modelClass,
            ])
            ->andReturn($newTargetStatistic);

        $result = $this->service->synchronizeTargetStatistics($model);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->count());
        $this->assertSame($existingTargetStatistic, $result->first());
        $this->assertSame($newTargetStatistic, $result->last());
    }
}