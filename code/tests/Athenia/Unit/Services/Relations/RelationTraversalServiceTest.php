<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Relations;

use App\Athenia\Services\Relations\RelationTraversalService;
use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Models\Collection\Collection as CollectionModel;
use App\Models\Collection\CollectionItem;

/**
 * Class RelationTraversalServiceTest
 * @package Tests\Athenia\Unit\Services\Relations
 */
class RelationTraversalServiceTest extends TestCase
{
    /**
     * @var RelationTraversalService
     */
    private RelationTraversalService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new RelationTraversalService();
    }

    public function testTraverseRelationsWithEmptyPath()
    {
        $collection = new CollectionModel();
        $collection->id = 1;

        $result = $this->service->traverseRelations($collection, '');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($collection, $result->first());
    }

    public function testTraverseRelationsWithSingleRelation()
    {
        $collection = new CollectionModel();
        $collection->id = 1;

        $collectionItem = new CollectionItem();
        $collectionItem->id = 2;
        $collectionItem->collection_id = $collection->id;

        $collection->setRelation('items', new Collection([$collectionItem]));

        $result = $this->service->traverseRelations($collection, 'items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($collectionItem, $result->first());
    }

    public function testTraverseRelationsWithNestedRelations()
    {
        $collection = new CollectionModel();
        $collection->id = 1;

        $parentItem = new CollectionItem();
        $parentItem->id = 2;
        $parentItem->collection_id = $collection->id;

        $childItem = new CollectionItem();
        $childItem->id = 3;
        $childItem->collection_id = $collection->id;

        $parentItem->setRelation('children', new Collection([$childItem]));
        $collection->setRelation('items', new Collection([$parentItem]));

        $result = $this->service->traverseRelations($collection, 'items.children');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($childItem, $result->first());
    }

    public function testTraverseRelationsWithMixedRelationTypes()
    {
        $collection = new CollectionModel();
        $collection->id = 1;

        $collectionItem1 = new CollectionItem();
        $collectionItem1->id = 2;
        $collectionItem1->collection_id = $collection->id;

        $collectionItem2 = new CollectionItem();
        $collectionItem2->id = 3;
        $collectionItem2->collection_id = $collection->id;

        $collection->setRelation('items', new Collection([$collectionItem1, $collectionItem2]));

        $result = $this->service->traverseRelations($collection, 'items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->count());
        $this->assertSame($collectionItem1, $result->first());
        $this->assertSame($collectionItem2, $result->last());
    }

    public function testTraverseRelationsWithPreloadedRelations()
    {
        $collection = new CollectionModel();
        $collection->id = 1;

        $collectionItem = new CollectionItem();
        $collectionItem->id = 2;
        $collectionItem->collection_id = $collection->id;

        $collection->setRelation('items', new Collection([$collectionItem]));

        $result = $this->service->traverseRelations($collection, 'items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($collectionItem, $result->first());
    }
} 