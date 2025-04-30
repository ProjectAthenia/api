<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Traits;

use App\Athenia\Models\Traits\HasStatistics;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tests\TestCase;

/**
 * Class HasStatisticsTest
 * @package Tests\Athenia\Unit\Models\Traits
 */
class HasStatisticsTest extends TestCase
{
    public function testTargetStatisticsRelationship()
    {
        $model = new class extends Model {
            use HasStatistics;
        };

        $relation = $model->targetStatistics();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('target_type', $relation->getMorphType());
        $this->assertEquals('target_id', $relation->getForeignKeyName());
        $this->assertEquals(TargetStatistic::class, get_class($relation->getRelated()));
    }

    public function testStatisticsRelationshipUsesMorphMany()
    {
        $model = new class extends Model {
            use HasStatistics;
        };

        $relation = $model->statistics();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('target_type', $relation->getMorphType());
        $this->assertEquals('target_id', $relation->getForeignKeyName());
        $this->assertEquals(TargetStatistic::class, get_class($relation->getRelated()));
    }

    public function testGetStatistic()
    {
        $model = new class extends Model {
            use HasStatistics;
        };

        $statisticId = 123;
        $statistic = new TargetStatistic();

        $morphMany = mock(MorphMany::class);
        $morphMany->shouldReceive('where')
            ->with('statistic_id', $statisticId)
            ->once()
            ->andReturnSelf();
        $morphMany->shouldReceive('first')
            ->once()
            ->andReturn($statistic);

        $model->shouldReceive('statistics')
            ->once()
            ->andReturn($morphMany);

        $result = $model->getStatistic($statisticId);

        $this->assertSame($statistic, $result);
    }
} 