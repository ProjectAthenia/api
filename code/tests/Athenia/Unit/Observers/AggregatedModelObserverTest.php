<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Observers;

use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Athenia\Jobs\Statistics\ProcessTargetStatisticsJob;
use App\Athenia\Observers\AggregatedModelObserver;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AggregatedModelObserverTest extends TestCase
{
    private RelationTraversalServiceContract|MockInterface $relationTraversalService;
    private Dispatcher|MockInterface $dispatcher;
    private AggregatedModelObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->relationTraversalService = Mockery::mock(RelationTraversalServiceContract::class);
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->observer = new AggregatedModelObserver(
            $this->relationTraversalService,
            $this->dispatcher
        );
    }

    /**
     * @dataProvider modelEventProvider
     */
    public function testModelEventsDispatchJobForStatisticTarget(string $event): void
    {
        $collection = new Collection([
            'id' => 1,
            'name' => 'Test Collection',
            'owner_id' => 1,
            'owner_type' => 'user',
            'is_public' => true,
        ]);

        $collectionItem = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_id' => 1,
            'item_type' => 'article',
            'order' => 1,
        ]);
        
        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collectionItem, 'collection')
            ->andReturn(new EloquentCollection([$collection]));

        $this->dispatcher->shouldReceive('dispatch')
            ->with(Mockery::type(ProcessTargetStatisticsJob::class))
            ->once();

        $this->observer->$event($collectionItem);
    }

    /**
     * @dataProvider modelEventProvider
     */
    public function testModelEventsDoNotDispatchJobForNonStatisticTarget(string $event): void
    {
        $collectionItem = new CollectionItem([
            'id' => 1,
            'collection_id' => 1,
            'item_id' => 1,
            'item_type' => 'article',
            'order' => 1,
        ]);
        
        $this->relationTraversalService->shouldReceive('traverseRelations')
            ->with($collectionItem, 'collection')
            ->andReturn(new EloquentCollection([new \stdClass()]));

        $this->dispatcher->shouldNotReceive('dispatch');

        $this->observer->$event($collectionItem);
    }

    public static function modelEventProvider(): array
    {
        return [
            'created event' => ['created'],
            'updated event' => ['updated'],
            'deleted event' => ['deleted'],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 