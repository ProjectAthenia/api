<?php

declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Statistics;

use App\Athenia\Events\Statistics\StatisticDeletedEvent;
use App\Models\Statistic\Statistic;
use Tests\TestCase;

class StatisticDeletedEventTest extends TestCase
{
    public function testGetStatistic(): void
    {
        $statistic = new Statistic();
        $event = new StatisticDeletedEvent($statistic);
        
        $this->assertSame($statistic, $event->getStatistic());
    }
} 