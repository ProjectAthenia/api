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
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

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

        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->id = 456;

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);

        /** @var CanBeStatisticTargetContract|Model|MockInterface $model */
        $model = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $model->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($modelId);
        $model->shouldReceive('targetStatistics')
            ->andReturn(new Collection());

        $this->statisticRepository->shouldReceive('findWhere')
            ->with(['model' => $modelClass])
            ->andReturn(collect([$statistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->with([
                'statistic_id' => $statistic->id,
                'target_id' => $modelId,
                'target_type' => $modelClass,
            ])
            ->andReturn($targetStatistic);

        $result = $this->service->synchronizeTargetStatistics($model);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($targetStatistic, $result->first());
    }

    public function testSynchronizeTargetStatisticsWithExistingTargets()
    {
        $modelClass = 'App\Models\TestModel';
        $modelId = 123;

        /** @var Statistic|MockInterface $existingStatistic */
        $existingStatistic = Mockery::mock(Statistic::class);
        $existingStatistic->id = 456;

        /** @var Statistic|MockInterface $newStatistic */
        $newStatistic = Mockery::mock(Statistic::class);
        $newStatistic->id = 789;

        /** @var TargetStatistic|MockInterface $existingTargetStatistic */
        $existingTargetStatistic = Mockery::mock(TargetStatistic::class);
        $existingTargetStatistic->statistic_id = $existingStatistic->id;

        /** @var TargetStatistic|MockInterface $newTargetStatistic */
        $newTargetStatistic = Mockery::mock(TargetStatistic::class);

        /** @var CanBeStatisticTargetContract|Model|MockInterface $model */
        $model = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $model->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($modelId);
        $model->shouldReceive('targetStatistics')
            ->andReturn(new Collection([$existingTargetStatistic]));

        $this->statisticRepository->shouldReceive('findWhere')
            ->with(['model' => $modelClass])
            ->andReturn(collect([$existingStatistic, $newStatistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->with([
                'statistic_id' => $newStatistic->id,
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