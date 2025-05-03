<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Events\Statistics\StatisticCreatedEvent;

class StatisticCreatedListener
{
    public function handle(StatisticCreatedEvent $event)
    {
        // Add logic for handling statistic creation if needed
    }
} 