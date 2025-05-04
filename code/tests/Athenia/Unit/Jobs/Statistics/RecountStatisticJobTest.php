<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Jobs\Statistics;

use App\Athenia\Contracts\Services\Statistics\SingleTargetStatisticProcessingServiceContract;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RecountStatisticJobTest extends TestCase
{
    public function testHandleProcessesAllTargetStatistics()
    {
        // Create mock target statistics
        $targetStatistics = [
            Mockery::mock(TargetStatistic::class),
            Mockery::mock(TargetStatistic::class),
        ];

        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection($targetStatistics));

        /** @var SingleTargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(SingleTargetStatisticProcessingServiceContract::class);
        
        // Setup expectations for each statistic
        foreach ($targetStatistics as $targetStatistic) {
            $processingService->shouldReceive('processSingleTargetStatistic')
                ->with($targetStatistic)
                ->once();
        }

        $job = new RecountStatisticJob($statistic);
        $job->handle($processingService);
    }

    public function testHandleWithNoTargetStatistics()
    {
        /** @var Statistic|MockInterface $statistic */
        $statistic = Mockery::mock(Statistic::class);
        $statistic->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection([]));

        /** @var SingleTargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(SingleTargetStatisticProcessingServiceContract::class);
        $processingService->shouldNotReceive('processSingleTargetStatistic');

        $job = new RecountStatisticJob($statistic);
        $job->handle($processingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 