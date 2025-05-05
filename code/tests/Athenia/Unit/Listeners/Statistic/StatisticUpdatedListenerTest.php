<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Statistic;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use App\Athenia\Listeners\Statistics\StatisticUpdatedListener;
use App\Models\Statistics\Statistic;
use Illuminate\Contracts\Bus\Dispatcher;
use Mockery;
use Tests\TestCase;

/**
 * Class StatisticUpdatedListenerTest
 * @package Tests\Athenia\Unit\Listeners\Statistic
 */
class StatisticUpdatedListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $statistic = new Statistic();
        $statistic->id = 234;
        $event = new StatisticUpdatedEvent($statistic);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->with(Mockery::on(function (RecountStatisticJob $job) {
                return $job->getStatistic()->id === 234;
            }))
            ->once();

        $listener = new StatisticUpdatedListener($dispatcher);
        $listener->handle($event);
    }
} 