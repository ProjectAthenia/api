<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Services\Statistics\StatisticRelationTraversalService;
use App\Models\Statistics\Statistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class StatisticRelationTraversalServiceTest
 * @package Tests\Athenia\Unit\Services\Statistics
 */
class StatisticRelationTraversalServiceTest extends TestCase
{
    /**
     * @var StatisticRelationTraversalService
     */
    private StatisticRelationTraversalService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticRelationTraversalService();
    }

    public function testGetRelatedModelsWithEmptyRelation()
    {
        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->relation = '';

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);

        $result = $this->service->getRelatedModels($statistic, $target);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($target, $result->first());
    }

    public function testGetRelatedModelsWithSingleRelation()
    {
        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->relation = 'items';

        /** @var Model|MockInterface $relatedModel */
        $relatedModel = Mockery::mock(Model::class);

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('relationLoaded')
            ->with('items')
            ->andReturn(false);
        $target->shouldReceive('load')
            ->with('items')
            ->once();
        $target->shouldReceive('getAttribute')
            ->with('items')
            ->andReturn(collect([$relatedModel]));

        $result = $this->service->getRelatedModels($statistic, $target);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($relatedModel, $result->first());
    }

    public function testGetRelatedModelsWithNestedRelations()
    {
        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->relation = 'parent.children';

        /** @var Model|MockInterface $childModel */
        $childModel = Mockery::mock(Model::class);

        /** @var Model|MockInterface $parentModel */
        $parentModel = Mockery::mock(Model::class);
        $parentModel->shouldReceive('relationLoaded')
            ->with('children')
            ->andReturn(false);
        $parentModel->shouldReceive('load')
            ->with('children')
            ->once();
        $parentModel->shouldReceive('getAttribute')
            ->with('children')
            ->andReturn(collect([$childModel]));

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('relationLoaded')
            ->with('parent')
            ->andReturn(false);
        $target->shouldReceive('load')
            ->with('parent')
            ->once();
        $target->shouldReceive('getAttribute')
            ->with('parent')
            ->andReturn($parentModel);

        $result = $this->service->getRelatedModels($statistic, $target);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertSame($childModel, $result->first());
    }

    public function testGetRelatedModelsWithMixedRelationTypes()
    {
        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->relation = 'hasMany.belongsTo';

        /** @var Model|MockInterface $finalModel */
        $finalModel = Mockery::mock(Model::class);

        /** @var Model|MockInterface $intermediateModel1 */
        $intermediateModel1 = Mockery::mock(Model::class);
        $intermediateModel1->shouldReceive('relationLoaded')
            ->with('belongsTo')
            ->andReturn(false);
        $intermediateModel1->shouldReceive('load')
            ->with('belongsTo')
            ->once();
        $intermediateModel1->shouldReceive('getAttribute')
            ->with('belongsTo')
            ->andReturn($finalModel);

        /** @var Model|MockInterface $intermediateModel2 */
        $intermediateModel2 = Mockery::mock(Model::class);
        $intermediateModel2->shouldReceive('relationLoaded')
            ->with('belongsTo')
            ->andReturn(false);
        $intermediateModel2->shouldReceive('load')
            ->with('belongsTo')
            ->once();
        $intermediateModel2->shouldReceive('getAttribute')
            ->with('belongsTo')
            ->andReturn($finalModel);

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('relationLoaded')
            ->with('hasMany')
            ->andReturn(false);
        $target->shouldReceive('load')
            ->with('hasMany')
            ->once();
        $target->shouldReceive('getAttribute')
            ->with('hasMany')
            ->andReturn(collect([$intermediateModel1, $intermediateModel2]));

        $result = $this->service->getRelatedModels($statistic, $target);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->count());
        $this->assertSame($finalModel, $result->first());
        $this->assertSame($finalModel, $result->last());
    }
} 