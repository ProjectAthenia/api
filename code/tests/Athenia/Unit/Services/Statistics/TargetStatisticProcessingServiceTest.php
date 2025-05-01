<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Services\Statistics\StatisticRelationTraversalServiceContract;
use App\Athenia\Services\Statistics\TargetStatisticProcessingService;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class TargetStatisticProcessingServiceTest
 * @package Tests\Athenia\Unit\Services\Statistics
 */
class TargetStatisticProcessingServiceTest extends TestCase
{
    /**
     * @var StatisticRelationTraversalServiceContract|MockInterface
     */
    private MockInterface $relationTraversalService;

    /**
     * @var TargetStatisticProcessingService
     */
    private TargetStatisticProcessingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->relationTraversalService = Mockery::mock(StatisticRelationTraversalServiceContract::class);
        $this->service = new TargetStatisticProcessingService($this->relationTraversalService);
    }

    public function testProcessTargetStatisticWithTotalCount()
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

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);
        $targetStatistic->statistic = $statistic;

        $this->relationTraversalService->shouldReceive('getRelatedModels')
            ->with($statistic, $targetStatistic->target)
            ->andReturn($relatedModels);

        $result = $this->service->processTargetStatistic($targetStatistic);

        $this->assertEquals(['total' => 1], $result);
    }

    public function testProcessTargetStatisticWithUniqueValues()
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

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);
        $targetStatistic->statistic = $statistic;

        $this->relationTraversalService->shouldReceive('getRelatedModels')
            ->with($statistic, $targetStatistic->target)
            ->andReturn($relatedModels);

        $result = $this->service->processTargetStatistic($targetStatistic);

        $expected = [
            'category1' => 1,
            'category2' => 1,
        ];
        $this->assertEquals($expected, $result);
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
} 