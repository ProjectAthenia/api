<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Statistic;

use App\Athenia\Events\Statistic\StatisticUpdatedEvent;
use App\Athenia\Jobs\Statistic\RecountStatisticJob;
use App\Athenia\Listeners\Statistic\StatisticUpdatedListener;
use App\Models\Statistic\Statistic;
use Illuminate\Bus\Dispatcher;
use Mockery;
use Mockery\MockInterface;
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

        /** @var Dispatcher|MockInterface $dispatcher */
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->with(Mockery::type(RecountStatisticJob::class))
            ->once();

        $listener = new StatisticUpdatedListener($dispatcher);
        $listener->handle($event);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 