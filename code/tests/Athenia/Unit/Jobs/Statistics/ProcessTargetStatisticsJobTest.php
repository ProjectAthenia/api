<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Jobs\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Services\Statistics\TargetStatisticProcessingServiceContract;
use App\Athenia\Jobs\Statistics\ProcessTargetStatisticsJob;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ProcessTargetStatisticsJobTest
 * @package Tests\Athenia\Unit\Jobs\Statistics
 */
class ProcessTargetStatisticsJobTest extends TestCase
{
    public function testHandleProcessesAllTargetStatistics()
    {
        // Create mock target statistics
        $targetStatistics = [
            Mockery::mock(TargetStatistic::class),
            Mockery::mock(TargetStatistic::class),
        ];

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection($targetStatistics));

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
        
        // Setup expectations for each statistic
        foreach ($targetStatistics as $targetStatistic) {
            $processingService->shouldReceive('processSingleTargetStatistic')
                ->with($targetStatistic)
                ->once();
        }

        $job = new ProcessTargetStatisticsJob($target);
        $job->handle($processingService);
    }

    public function testHandleWithNoTargetStatistics()
    {
        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection([]));

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
        $processingService->shouldNotReceive('processSingleTargetStatistic');

        $job = new ProcessTargetStatisticsJob($target);
        $job->handle($processingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 