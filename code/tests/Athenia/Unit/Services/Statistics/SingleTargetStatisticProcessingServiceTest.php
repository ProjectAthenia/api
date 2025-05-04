<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Athenia\Services\Statistics\SingleTargetStatisticProcessingService;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Tests\TestCase;

class SingleTargetStatisticProcessingServiceTest extends TestCase
{
    private $relationTraversalService;
    private $targetStatisticRepository;
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
        $collection = new Collection([
            'id' => 1,
            'name' => 'Test Collection'
        ]);

        $item1 = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_type' => 'article',
            'item_id' => 1
        ]);

        $item2 = new CollectionItem([
            'id' => 2,
            'collection_id' => 1,
            'item_type' => 'article',
            'item_id' => 2
        ]);

        $statistic = new Statistic([
            'id' => 1,
            'name' => 'test_statistic',
            'type' => 'total_count',
            'relation' => 'collectionItems'
        ]);

        $filter = new StatisticFilter([
            'id' => 1,
            'statistic_id' => 1,
            'category' => 'test_category',
            'value' => 'test_value'
        ]);

        $statistic->setRelation('filters', new EloquentCollection([$filter]));

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => Collection::class,
            'statistic_id' => 1,
            'value' => 0,
            'target' => $collection,
            'statistic' => $statistic
        ]);

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collection, 'collectionItems')
            ->andReturn(new EloquentCollection([$item1, $item2]));

        $this->targetStatisticRepository->shouldReceive('update')
            ->with($targetStatistic, ['result' => ['total' => 2]])
            ->once();

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    public function testProcessSingleTargetStatisticWithUniqueValues()
    {
        $collection = new Collection([
            'id' => 1,
            'name' => 'Test Collection'
        ]);

        $item1 = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_type' => 'article',
            'item_id' => 1
        ]);

        $item2 = new CollectionItem([
            'id' => 2,
            'collection_id' => 1,
            'item_type' => 'article',
            'item_id' => 2
        ]);

        $statistic = new Statistic([
            'id' => 1,
            'name' => 'test_statistic',
            'type' => 'total_count',
            'relation' => 'collectionItems'
        ]);

        $uniqueFilter = new StatisticFilter([
            'id' => 1,
            'statistic_id' => 1,
            'category' => 'item_type',
            'operator' => 'unique'
        ]);

        $statistic->setRelation('filters', new EloquentCollection([$uniqueFilter]));

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => Collection::class,
            'statistic_id' => 1,
            'value' => 0,
            'target' => $collection,
            'statistic' => $statistic
        ]);

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collection, 'collectionItems')
            ->andReturn(new EloquentCollection([$item1, $item2]));

        $this->targetStatisticRepository->shouldReceive('update')
            ->with($targetStatistic, ['result' => ['article' => 2]])
            ->once();

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 