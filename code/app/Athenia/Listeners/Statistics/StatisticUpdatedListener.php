<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use Illuminate\Contracts\Bus\Dispatcher;

class StatisticUpdatedListener
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {
    }

    public function handle(StatisticUpdatedEvent $event)
    {
        $statistic = $event->getStatistic();
        $statistic->unsetRelations();
        $this->dispatcher->dispatch(new RecountStatisticJob($statistic));
    }
} 