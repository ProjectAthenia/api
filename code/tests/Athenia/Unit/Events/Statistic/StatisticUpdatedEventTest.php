<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Statistic;

use App\Athenia\Events\Statistic\StatisticUpdatedEvent;
use App\Models\Statistic\Statistic;
use Tests\TestCase;

/**
 * Class StatisticUpdatedEventTest
 * @package Tests\Athenia\Unit\Events\Statistics
 */
class StatisticUpdatedEventTest extends TestCase
{
    public function testGetStatistic(): void
    {
        $statistic = new Statistic();
        $event = new StatisticUpdatedEvent($statistic);
        
        $this->assertSame($statistic, $event->getStatistic());
    }
} 