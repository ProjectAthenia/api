<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Traits;

use App\Athenia\Models\Traits\HasStatisticTargets;
use App\Models\Statistic\TargetStatistic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tests\TestCase;

/**
 * Class HasStatisticTargetsTest
 * @package Tests\Athenia\Unit\Models\Traits
 */
class HasStatisticTargetsTest extends TestCase
{
    public function testTargetStatisticsRelationship()
    {
        $model = new class extends Model {
            use HasStatisticTargets;
        };

        $relation = $model->targetStatistics();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('target_type', $relation->getMorphType());
        $this->assertEquals('target_id', $relation->getForeignKeyName());
        $this->assertEquals(TargetStatistic::class, get_class($relation->getRelated()));
    }
} 