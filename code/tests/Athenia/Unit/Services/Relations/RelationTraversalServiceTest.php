<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Relations;

use App\Athenia\Services\Relations\RelationTraversalService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

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
        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);

        $result = $this->service->traverseRelations($model, '');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($model, $result->first());
    }

    public function testTraverseRelationsWithSingleRelation()
    {
        /** @var Model|MockInterface $relatedModel */
        $relatedModel = Mockery::mock(Model::class);

        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('relationLoaded')
            ->with('items')
            ->andReturn(false);
        $model->shouldReceive('load')
            ->with('items')
            ->once();
        $model->shouldReceive('getAttribute')
            ->with('items')
            ->andReturn(collect([$relatedModel]));

        $result = $this->service->traverseRelations($model, 'items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($relatedModel, $result->first());
    }

    public function testTraverseRelationsWithNestedRelations()
    {
        /** @var Model|MockInterface $finalModel */
        $finalModel = Mockery::mock(Model::class);

        /** @var Model|MockInterface $intermediateModel */
        $intermediateModel = Mockery::mock(Model::class);
        $intermediateModel->shouldReceive('relationLoaded')
            ->with('children')
            ->andReturn(false);
        $intermediateModel->shouldReceive('load')
            ->with('children')
            ->once();
        $intermediateModel->shouldReceive('getAttribute')
            ->with('children')
            ->andReturn(collect([$finalModel]));

        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('relationLoaded')
            ->with('parent')
            ->andReturn(false);
        $model->shouldReceive('load')
            ->with('parent')
            ->once();
        $model->shouldReceive('getAttribute')
            ->with('parent')
            ->andReturn($intermediateModel);

        $result = $this->service->traverseRelations($model, 'parent.children');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($finalModel, $result->first());
    }

    public function testTraverseRelationsWithMixedRelationTypes()
    {
        /** @var Model|MockInterface $finalModel1 */
        $finalModel1 = Mockery::mock(Model::class);
        /** @var Model|MockInterface $finalModel2 */
        $finalModel2 = Mockery::mock(Model::class);

        /** @var Model|MockInterface $intermediateModel */
        $intermediateModel = Mockery::mock(Model::class);
        $intermediateModel->shouldReceive('relationLoaded')
            ->with('items')
            ->andReturn(false);
        $intermediateModel->shouldReceive('load')
            ->with('items')
            ->once();
        $intermediateModel->shouldReceive('getAttribute')
            ->with('items')
            ->andReturn(collect([$finalModel1, $finalModel2]));

        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('relationLoaded')
            ->with('owner')
            ->andReturn(false);
        $model->shouldReceive('load')
            ->with('owner')
            ->once();
        $model->shouldReceive('getAttribute')
            ->with('owner')
            ->andReturn($intermediateModel);

        $result = $this->service->traverseRelations($model, 'owner.items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->count());
        $this->assertSame($finalModel1, $result->first());
        $this->assertSame($finalModel2, $result->last());
    }

    public function testTraverseRelationsWithPreloadedRelations()
    {
        /** @var Model|MockInterface $finalModel */
        $finalModel = Mockery::mock(Model::class);

        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('relationLoaded')
            ->with('items')
            ->andReturn(true);
        $model->shouldReceive('getAttribute')
            ->with('items')
            ->andReturn(collect([$finalModel]));

        $result = $this->service->traverseRelations($model, 'items');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($finalModel, $result->first());
    }
} 