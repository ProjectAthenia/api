<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Statistic;

use App\Models\Statistic\StatisticFilter;
use App\Models\Statistic\Statistic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class StatisticFilterTest extends TestCase
{
    public function testStatisticRelation(): void
    {
        $model = new StatisticFilter();
        $relation = $model->statistic();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Statistic::class, get_class($relation->getRelated()));
    }
} 