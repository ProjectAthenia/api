<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Statistics;

use App\Models\Statistics\TargetStatistic;
use App\Models\Statistics\Statistic;
use App\Models\User;
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

    public function testFactoryCreatesValidModel()
    {
        $targetStatistic = TargetStatistic::factory()->create();

        $this->assertNotNull($targetStatistic->target_id);
        $this->assertEquals(User::class, $targetStatistic->target_type);
        $this->assertNotNull($targetStatistic->statistic_id);
        $this->assertNotNull($targetStatistic->value);
    }

    public function testFactoryWithCustomTarget()
    {
        $user = User::factory()->create();
        $targetStatistic = TargetStatistic::factory()
            ->forTarget($user->id, User::class)
            ->create();

        $this->assertEquals($user->id, $targetStatistic->target_id);
        $this->assertEquals(User::class, $targetStatistic->target_type);
    }
} 