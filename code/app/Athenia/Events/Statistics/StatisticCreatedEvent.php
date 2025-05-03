<?php

declare(strict_types=1);

namespace App\Athenia\Events\Statistics;

use App\Models\Statistics\Statistic;

class StatisticCreatedEvent
{
    public Statistic $statistic;

    public function __construct(Statistic $statistic)
    {
        $this->statistic = $statistic;
    }
} 