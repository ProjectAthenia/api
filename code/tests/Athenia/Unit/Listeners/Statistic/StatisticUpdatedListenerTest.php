<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Statistic;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use App\Athenia\Listeners\Statistics\StatisticUpdatedListener;
use App\Models\Statistics\Statistic;
use Illuminate\Contracts\Bus\Dispatcher;
use Tests\TestCase;

/**
 * Class StatisticUpdatedListenerTest
 * @package Tests\Athenia\Unit\Listeners\Statistic
 */
class StatisticUpdatedListenerTest extends TestCase
{
    public function testHandleDispatchesJob()
    {
        $dispatcher = mock(Dispatcher::class);
        $listener = new StatisticUpdatedListener($dispatcher);

        $statistic = new Statistic([
            'id' => 234,
        ]);
        $event = new StatisticUpdatedEvent($statistic);

        $dispatcher->shouldReceive('dispatch')->once()->with(\Mockery::on(function (RecountStatisticJob $job) {
            return $job->statistic->id === 234;
        }));

        $listener->handle($event);
    }
} 