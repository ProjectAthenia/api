<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Events\Statistics\StatisticDeletedEvent;
use App\Models\Statistics\TargetStatistic;

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