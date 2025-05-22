<?php

declare(strict_types=1);

namespace App\Athenia\Events\Statistics;

use App\Models\Statistic\Statistic;

class StatisticUpdatedEvent
{
    public function __construct(
        private readonly Statistic $statistic
    ) {}

    public function getStatistic(): Statistic
    {
        return $this->statistic;
    }
} 