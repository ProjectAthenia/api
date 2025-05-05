<?php

declare(strict_types=1);

namespace App\Athenia\Listeners\Statistics;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use Illuminate\Contracts\Bus\Dispatcher;

class StatisticUpdatedListener
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(StatisticUpdatedEvent $event)
    {
        $statistic = $event->getStatistic();
        $statistic->unsetRelations();
        $this->dispatcher->dispatch(new RecountStatisticJob($statistic));
    }
} 