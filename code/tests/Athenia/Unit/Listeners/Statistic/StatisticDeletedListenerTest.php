<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Statistic;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Events\Statistics\StatisticDeletedEvent;
use App\Athenia\Listeners\Statistics\StatisticDeletedListener;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class StatisticDeletedListenerTest extends TestCase
{
    public function testHandleDeletesAllTargetStatistics(): void
    {
        // Create a statistic with some target statistics
        $statistic = new Statistic();
        $statistic->id = 1;
        
        $targetStatistics = new Collection([
            new TargetStatistic(['id' => 1]),
            new TargetStatistic(['id' => 2]),
            new TargetStatistic(['id' => 3]),
        ]);
        
        $statistic->setRelation('targetStatistics', $targetStatistics);
        
        $event = new StatisticDeletedEvent($statistic);

        /** @var TargetStatisticRepositoryContract|MockInterface $targetStatisticRepository */
        $targetStatisticRepository = Mockery::mock(TargetStatisticRepositoryContract::class);
        $targetStatisticRepository->shouldReceive('delete')
            ->times(3)
            ->with(Mockery::type(TargetStatistic::class));

        $listener = new StatisticDeletedListener($targetStatisticRepository);
        $listener->handle($event);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 