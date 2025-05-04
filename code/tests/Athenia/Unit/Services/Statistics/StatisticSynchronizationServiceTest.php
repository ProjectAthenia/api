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
use App\Athenia\Models\Traits\HasStatistics;

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
            public function morphRelationName(): string { return 'test_model'; }
        };
        $model->targetStatistics = new BaseCollection();

        $this->statisticRepository->shouldReceive('findAll')
            ->with(['model' => 'test_model'])
            ->once()
            ->andReturn(new BaseCollection([$statistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->once()
            ->with([
                'statistic_id' => 456,
                'target_id' => 123,
                'target_type' => 'test_model',
            ])
            ->andReturn($targetStatistic);

        $result = $this->service->synchronizeTargetStatistics($model);

        $this->assertInstanceOf(BaseCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(456, $result->first()->statistic_id);
    }

    public function testSynchronizeTargetStatisticsWithExistingTargets()
    {
        $existingStatistic = new Statistic([
            'id' => 456,
        ]);

        $newStatistic = new Statistic([
            'id' => 789,
        ]);

        $existingTargetStatistic = new TargetStatistic([
            'id' => 1,
            'statistic_id' => 456,
            'target_id' => 123,
            'target_type' => 'test_model',
        ]);

        $newTargetStatistic = new TargetStatistic([
            'id' => 2,
            'statistic_id' => 789,
            'target_id' => 123,
            'target_type' => 'test_model',
        ]);

        $existingTargetStatistics = new Collection([$existingTargetStatistic]);

        $model = new class extends Model implements CanBeStatisticTargetContract {
            use HasStatistics;
            public $id = 123;
            public $targetStatistics;
            public function morphRelationName(): string { return 'test_model'; }
        };
        $model->targetStatistics = $existingTargetStatistics;

        $this->statisticRepository->shouldReceive('findAll')
            ->with(['model' => 'test_model'])
            ->andReturn(collect([$existingStatistic, $newStatistic]));

        $this->targetStatisticRepository->shouldReceive('create')
            ->with([
                'statistic_id' => 789,
                'target_id' => 123,
                'target_type' => 'test_model',
            ])
            ->andReturn($newTargetStatistic);

        $result = $this->service->synchronizeTargetStatistics($model);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->count());
        $this->assertSame($existingTargetStatistic, $result->first());
        $this->assertSame($newTargetStatistic, $result->last());
    }
}