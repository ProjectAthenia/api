<?php

declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Statistic;

use App\Athenia\Events\Statistic\StatisticCreatedEvent;
use App\Models\Statistic\Statistic;
use Tests\TestCase;

class StatisticCreatedEventTest extends TestCase
{
    public function testGetStatistic(): void
    {
        $statistic = new Statistic();
        $event = new StatisticCreatedEvent($statistic);
        
        $this->assertSame($statistic, $event->getStatistic());
    }
} 