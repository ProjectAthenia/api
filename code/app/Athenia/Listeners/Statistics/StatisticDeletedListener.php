<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Events\Statistics\StatisticDeletedEvent;

class StatisticDeletedListener
{
    public function handle(StatisticDeletedEvent $event)
    {
        // Add logic for handling statistic deletion if needed
    }
} 