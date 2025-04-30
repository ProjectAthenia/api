<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Statistics;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Models\Statistics\Statistic;
use Tests\TestCase;

/**
 * Class StatisticUpdatedEventTest
 * @package Tests\Athenia\Unit\Events\Statistics
 */
class StatisticUpdatedEventTest extends TestCase
{
    public function testModelGetterReturnsModel()
    {
        $model = new Statistic();
        $event = new StatisticUpdatedEvent($model);

        $this->assertEquals($model, $event->statistic);
    }
} 