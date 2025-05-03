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
} 