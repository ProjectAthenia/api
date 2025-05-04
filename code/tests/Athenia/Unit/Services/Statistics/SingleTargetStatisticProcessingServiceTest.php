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
        $item1 = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_id' => 1,
            'item_type' => 'article',
            'order' => 1,
        ]);

        $item2 = new CollectionItem([
            'id' => 2,
            'collection_id' => 1,
            'item_id' => 2,
            'item_type' => 'article',
            'order' => 2,
        ]);

        $collection = new Collection([
            'id' => 1,
            'name' => 'Test Collection',
            'owner_id' => 1,
            'owner_type' => 'user',
        ]);

        $filter = new StatisticFilter([
            'id' => 1,
            'statistic_id' => 1,
            'category' => 'test_category',
            'value' => 'article',
            'field' => 'item_type',
            'operator' => '='
        ]);

        $statistic = new Statistic([
            'id' => 1,
            'name' => 'Test Statistic',
            'relation' => 'collectionItems',
        ]);
        $statistic->setRelation('filters', new EloquentCollection([$filter]));

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => Collection::class,
            'statistic_id' => 1,
            'value' => 0,
        ]);
        $targetStatistic->exists = true;
        $targetStatistic->setRelation('target', $collection);
        $targetStatistic->setRelation('statistic', $statistic);

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collection, 'collectionItems')
            ->andReturn(new EloquentCollection([$item1, $item2]));

        $this->targetStatisticRepository->shouldReceive('update')
            ->withAnyArgs()
            ->once()
            ->andReturnUsing(function ($model, $data) use ($targetStatistic) {
                $this->assertSame($targetStatistic, $model);
                $this->assertArrayHasKey('result', $data);
                $this->assertArrayHasKey('total', $data['result']);
                $this->assertEquals(2, $data['result']['total']);
                return $model;
            });

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    public function testProcessSingleTargetStatisticWithUniqueValues()
    {
        $item1 = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_id' => 1,
            'item_type' => 'article',
            'order' => 1,
        ]);

        $item2 = new CollectionItem([
            'id' => 2,
            'collection_id' => 1,
            'item_id' => 2,
            'item_type' => 'article',
            'order' => 2,
        ]);

        $collection = new Collection([
            'id' => 1,
            'name' => 'Test Collection',
            'owner_id' => 1,
            'owner_type' => 'user',
        ]);

        $filter = new StatisticFilter([
            'id' => 1,
            'statistic_id' => 1,
            'category' => 'test_category',
            'value' => null,
            'field' => 'item_type',
            'operator' => 'unique'
        ]);

        $statistic = new Statistic([
            'id' => 1,
            'name' => 'Test Statistic',
            'relation' => 'collectionItems',
        ]);
        $statistic->setRelation('filters', new EloquentCollection([$filter]));

        $targetStatistic = new TargetStatistic([
            'id' => 1,
            'target_id' => 1,
            'target_type' => Collection::class,
            'statistic_id' => 1,
            'value' => 0,
        ]);
        $targetStatistic->exists = true;
        $targetStatistic->setRelation('target', $collection);
        $targetStatistic->setRelation('statistic', $statistic);

        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collection, 'collectionItems')
            ->andReturn(new EloquentCollection([$item1, $item2]));

        $this->targetStatisticRepository->shouldReceive('update')
            ->withAnyArgs()
            ->once()
            ->andReturnUsing(function ($model, $data) use ($targetStatistic) {
                $this->assertSame($targetStatistic, $model);
                $this->assertArrayHasKey('result', $data);
                $this->assertArrayHasKey('article', $data['result']);
                $this->assertEquals(2, $data['result']['article']);
                return $model;
            });

        $this->service->processSingleTargetStatistic($targetStatistic);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 