<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistic;

use App\Athenia\Contracts\Repositories\Statistic\TargetStatisticRepositoryContract;
use App\Athenia\Events\Statistic\StatisticDeletedEvent;
use App\Models\Statistic\TargetStatistic;

class StatisticDeletedListener
{
    public function __construct(
        private readonly TargetStatisticRepositoryContract $targetStatisticRepository
    ) {
    }

    public function handle(StatisticDeletedEvent $event): void
    {
        $statistic = $event->getStatistic();
        
        // Delete all target statistics related to this statistic
        foreach ($statistic->targetStatistics as $targetStatistic) {
            $this->targetStatisticRepository->delete($targetStatistic);
        }
    }
} 