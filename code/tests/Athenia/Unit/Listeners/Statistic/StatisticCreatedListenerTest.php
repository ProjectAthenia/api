<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Statistic;

use App\Athenia\Contracts\Services\Statistic\StatisticSynchronizationServiceContract;
use App\Athenia\Events\Statistic\StatisticCreatedEvent;
use App\Athenia\Jobs\Statistic\RecountStatisticJob;
use App\Athenia\Listeners\Statistic\StatisticCreatedListener;
use App\Models\Statistic\Statistic;
use App\Models\Statistic\TargetStatistic;
use Illuminate\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class StatisticCreatedListenerTest extends TestCase
{
    public function testHandleCreatesTargetStatisticsAndDispatchesRecountJob(): void
    {
        // Create a real Statistic model without saving
        $statistic = new Statistic();
        $statistic->id = 1;
        $statistic->name = 'Test Statistic';

        $event = new StatisticCreatedEvent($statistic);

        // Create mock target statistics
        $targetStatistics = new Collection([
            new TargetStatistic([
                'id' => 1,
                'target_id' => 1,
                'target_type' => 'collection',
                'statistic_id' => $statistic->id,
            ]),
        ]);

        /** @var StatisticSynchronizationServiceContract|MockInterface $synchronizationService */
        $synchronizationService = Mockery::mock(StatisticSynchronizationServiceContract::class);
        $synchronizationService->shouldReceive('createTargetStatisticsForStatistic')
            ->with($statistic)
            ->once()
            ->andReturn($targetStatistics);

        /** @var Dispatcher|MockInterface $dispatcher */
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->with(Mockery::type(RecountStatisticJob::class))
            ->once();

        $listener = new StatisticCreatedListener($dispatcher, $synchronizationService);
        $listener->handle($event);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 