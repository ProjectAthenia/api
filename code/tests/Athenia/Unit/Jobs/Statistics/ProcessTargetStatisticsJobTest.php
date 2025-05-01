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
        // Setup mock results
        $results = [
            ['total' => 42],
            ['total' => 24],
        ];

        // Create mock target statistics
        $targetStatistics = [];
        foreach ($results as $i => $result) {
            /** @var TargetStatistic|MockInterface $targetStatistic */
            $targetStatistic = Mockery::mock(TargetStatistic::class);
            $targetStatistic->shouldReceive('update')
                ->with(['result' => $result])
                ->once();
            $targetStatistics[] = $targetStatistic;
        }

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection($targetStatistics));

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
        
        // Setup expectations for each statistic
        foreach ($targetStatistics as $i => $targetStatistic) {
            $processingService->shouldReceive('processTargetStatistic')
                ->with($targetStatistic)
                ->andReturn($results[$i])
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
        $processingService->shouldNotReceive('processTargetStatistic');

        $job = new ProcessTargetStatisticsJob($target);
        $job->handle($processingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 