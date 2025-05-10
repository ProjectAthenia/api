<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Statistics;

use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class StatisticTest extends TestCase
{
    public function testStatisticFiltersRelation(): void
    {
        $model = new Statistic();
        $relation = $model->statisticFilters();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals(StatisticFilter::class, get_class($relation->getRelated()));
    }

    public function testTargetStatisticsRelation(): void
    {
        $model = new Statistic();
        $relation = $model->targetStatistics();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals(TargetStatistic::class, get_class($relation->getRelated()));
    }
} 