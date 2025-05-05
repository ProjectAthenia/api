<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Contracts\Services\Statistics\StatisticSynchronizationServiceContract;
use App\Athenia\Events\Statistics\StatisticCreatedEvent;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use Illuminate\Bus\Dispatcher;

class StatisticCreatedListener
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly StatisticSynchronizationServiceContract $synchronizationService
    ) {
    }

    public function handle(StatisticCreatedEvent $event): void
    {
        $statistic = $event->getStatistic();
        
        // Create target statistics for the new statistic
        $this->synchronizationService->createTargetStatisticsForStatistic($statistic);
        
        // Trigger a recount job to process the new target statistics
        $this->dispatcher->dispatch(new RecountStatisticJob($statistic));
    }
} 