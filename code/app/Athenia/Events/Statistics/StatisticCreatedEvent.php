<?php

declare(strict_types=1);

namespace App\Athenia\Events\Statistics;

use App\Models\Statistics\Statistic;

class StatisticCreatedEvent
{
    public function __construct(
        private readonly Statistic $statistic
    ) {}

    public function getStatistic(): Statistic
    {
        return $this->statistic;
    }
} 