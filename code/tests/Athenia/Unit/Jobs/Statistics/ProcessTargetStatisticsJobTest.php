<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Jobs\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Services\Statistics\TargetStatisticProcessingServiceContract;
use App\Jobs\Statistics\ProcessTargetStatisticsJob;
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
    public function testHandle()
    {
        $result = ['total' => 42];

        /** @var TargetStatistic|MockInterface $targetStatistic */
        $targetStatistic = Mockery::mock(TargetStatistic::class);
        $targetStatistic->shouldReceive('update')
            ->with(['result' => $result])
            ->once();

        /** @var CanBeStatisticTargetContract|Model|MockInterface $target */
        $target = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);
        $target->shouldReceive('getAttribute')
            ->with('targetStatistics')
            ->andReturn(new Collection([$targetStatistic]));

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
        $processingService->shouldReceive('processTargetStatistic')
            ->with($targetStatistic)
            ->andReturn($result);

        $job = new ProcessTargetStatisticsJob($target);
        $job->handle($processingService);
    }
} 