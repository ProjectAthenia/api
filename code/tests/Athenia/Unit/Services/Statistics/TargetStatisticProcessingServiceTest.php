<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Athenia\Services\Statistics\TargetStatisticProcessingService;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Models\TestModel;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use App\Contracts\Models\CanBeStatisticTargetContract;
use App\Traits\Models\HasStatistics;

/**
 * Class TargetStatisticProcessingServiceTest
 * @package Tests\Athenia\Unit\Services\Statistics
 */
class TargetStatisticProcessingServiceTest extends TestCase
{
    /**
     * @var RelationTraversalServiceContract|MockInterface
     */
    private MockInterface $relationTraversalService;

    /**
     * @var TargetStatisticRepositoryContract|MockInterface
     */
    private MockInterface $targetStatisticRepository;

    /**
     * @var TargetStatisticProcessingService
     */
    private TargetStatisticProcessingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->relationTraversalService = Mockery::mock(RelationTraversalServiceContract::class);
        $this->targetStatisticRepository = Mockery::mock(TargetStatisticRepositoryContract::class);
        $this->service = new TargetStatisticProcessingService(
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

        $filter = new StatisticFilter([
            'operator' => '>',
            'field' => 'value',
            'value' => '15',
        ]);

        $statistic = new Statistic([
            'relation' => 'test_relation',
        ]);
        $statistic->filters = collect([$filter]);

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => 'test_model',
            'statistic_id' => 1,
        ]);
        $targetStatistic->setRelation('statistic', $statistic);
        $targetStatistic->setRelation('target', new class extends Model {});

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

        $uniqueFilter = new StatisticFilter([
            'operator' => 'unique',
            'field' => 'category',
        ]);

        $valueFilter = new StatisticFilter([
            'operator' => '>',
            'field' => 'value',
            'value' => '15',
        ]);

        $statistic = new Statistic([
            'relation' => 'test_relation',
        ]);
        $statistic->filters = collect([$uniqueFilter, $valueFilter]);

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => 'test_model',
            'statistic_id' => 1,
        ]);
        $targetStatistic->setRelation('statistic', $statistic);
        $targetStatistic->setRelation('target', new class extends Model {});

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

    public function testProcessTargetStatistics()
    {
        $model = new class extends Model implements CanBeStatisticTargetContract {
            use HasStatistics;
            public $id = 123;
            public $targetStatistics;
            public function morphRelationName(): string { return 'App\Models\TestModel'; }
        };
        $model->targetStatistics = new BaseCollection();

        $statistic = new Statistic();
        $statistic->id = 456;

        $targetStatistic = new TargetStatistic();
        $targetStatistic->statistic_id = 456;
        $targetStatistic->target_id = 123;
        $targetStatistic->target_type = 'App\Models\TestModel';

        $this->relationTraversalService->shouldReceive('getRelatedModels')
            ->once()
            ->andReturn(new BaseCollection([$model]));

        $this->relationTraversalService->shouldReceive('getRelatedModels')
            ->once()
            ->andReturn(new BaseCollection([$statistic]));

        $this->relationTraversalService->shouldReceive('getRelatedModels')
            ->once()
            ->andReturn(new BaseCollection([$targetStatistic]));

        $result = $this->service->processTargetStatistics($model);

        $this->assertInstanceOf(BaseCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(456, $result->first()->statistic_id);
    }

    private function createModelWithValue(string $category, int $value): Model
    {
        return new class extends Model {
            protected $attributes = [];
            
            public function __construct() {
                parent::__construct();
                $this->attributes = [
                    'category' => func_get_arg(0),
                    'value' => func_get_arg(1),
                ];
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 