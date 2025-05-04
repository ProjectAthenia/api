<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Athenia\Services\Statistics\SingleTargetStatisticProcessingService;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SingleTargetStatisticProcessingServiceTest extends TestCase
{
    private MockInterface $relationTraversalService;
    private MockInterface $targetStatisticRepository;
    private SingleTargetStatisticProcessingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->relationTraversalService = Mockery::mock(RelationTraversalServiceContract::class);
        $this->targetStatisticRepository = Mockery::mock(TargetStatisticRepositoryContract::class);
        $this->service = new SingleTargetStatisticProcessingService(
            $this->relationTraversalService,
            $this->targetStatisticRepository
        );
    }

    public function testProcessSingleTargetStatisticWithTotalCount()
    {
        $relatedModels = collect([
            $this->createModelWithValue('test1', 10),
            $this->createModelWithValue('test2', 20),
        ]);

        /** @var StatisticFilter|MockInterface $filter */
        $filter = Mockery::mock(StatisticFilter::class);
        $filter->operator = '>';
        $filter->field = 'value';
        $filter->value = '15';

        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->filters = collect([$filter]);
        $statistic->relation = 'test_relation';

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);
        $targetStatistic->statistic = $statistic;
        $targetStatistic->target = new class extends Model {};

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($targetStatistic->target, 'test_relation')
            ->andReturn($relatedModels);

        $this->targetStatisticRepository->shouldReceive('update')
            ->with($targetStatistic, ['result' => ['total' => 1]])
            ->once();

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    public function testProcessSingleTargetStatisticWithUniqueValues()
    {
        $relatedModels = collect([
            $this->createModelWithValue('category1', 10),
            $this->createModelWithValue('category1', 20),
            $this->createModelWithValue('category2', 30),
        ]);

        /** @var StatisticFilter|MockInterface $uniqueFilter */
        $uniqueFilter = Mockery::mock(StatisticFilter::class);
        $uniqueFilter->operator = 'unique';
        $uniqueFilter->field = 'category';

        /** @var StatisticFilter|MockInterface $valueFilter */
        $valueFilter = Mockery::mock(StatisticFilter::class);
        $valueFilter->operator = '>';
        $valueFilter->field = 'value';
        $valueFilter->value = '15';

        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->filters = collect([$uniqueFilter, $valueFilter]);
        $statistic->relation = 'test_relation';

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);
        $targetStatistic->statistic = $statistic;
        $targetStatistic->target = new class extends Model {};

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($targetStatistic->target, 'test_relation')
            ->andReturn($relatedModels);

        $expectedResult = [
            'category1' => 1,
            'category2' => 1,
        ];

        $this->targetStatisticRepository->shouldReceive('update')
            ->with($targetStatistic, ['result' => $expectedResult])
            ->once();

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    private function createModelWithValue(string $category, int $value): Model
    {
        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')
            ->with('category')
            ->andReturn($category);
        $model->shouldReceive('getAttribute')
            ->with('value')
            ->andReturn($value);
        return $model;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 