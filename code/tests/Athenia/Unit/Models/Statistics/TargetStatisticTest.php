<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Statistics;

use App\Models\Statistic\TargetStatistic;
use App\Models\Statistic\Statistic;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class TargetStatisticTest
 * @package Tests\Athenia\Unit\Models\Statistics
 */
class TargetStatisticTest extends TestCase
{
    public function testTargetRelationship()
    {
        $model = new TargetStatistic();
        $relation = $model->target();

        $this->assertEquals('target_type', $relation->getMorphType());
        $this->assertEquals('target_id', $relation->getForeignKeyName());
    }

    public function testStatisticRelationship()
    {
        $model = new TargetStatistic();
        $relation = $model->statistic();

        $this->assertEquals('statistic_id', $relation->getForeignKeyName());
        $this->assertInstanceOf(Statistic::class, $relation->getRelated());
    }
} 