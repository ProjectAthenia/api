<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Jobs\Statistics;

use App\Athenia\Contracts\Services\Statistics\TargetStatisticProcessingServiceContract;
use App\Athenia\Jobs\Statistics\RecountStatisticJob;
use App\Models\Collection\Collection;
use App\Models\Statistic\Statistic;
use App\Models\Statistic\TargetStatistic;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RecountStatisticJobTest extends TestCase
{
    public function testHandleProcessesAllTargetStatistics(): void
    {
        // Create a real Collection model without saving
        $collection = new Collection();
        $collection->id = 1;
        $collection->name = 'Test Collection';

        // Create a real Statistic model without saving
        $statistic = new Statistic();
        $statistic->id = 1;
        $statistic->name = 'Test Statistic';

        // Create real TargetStatistic models without saving
        $targetStatistics = new EloquentCollection([
            new TargetStatistic([
                'id' => 1,
                'target_id' => $collection->id,
                'target_type' => 'collection',
                'statistic_id' => $statistic->id,
            ]),
            new TargetStatistic([
                'id' => 2,
                'target_id' => $collection->id,
                'target_type' => 'collection',
                'statistic_id' => $statistic->id,
            ]),
        ]);

        // Associate the target statistics with the statistic
        $statistic->setRelation('targetStatistics', $targetStatistics);

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
        
        // Setup expectations for each statistic
        foreach ($targetStatistics as $targetStatistic) {
            $processingService->shouldReceive('processSingleTargetStatistic')
                ->with($targetStatistic)
                ->once();
        }

        $job = new RecountStatisticJob($statistic);
        $job->handle($processingService);
    }

    public function testHandleWithNoTargetStatistics(): void
    {
        // Create a real Statistic model without saving
        $statistic = new Statistic();
        $statistic->id = 1;
        $statistic->name = 'Test Statistic';

        // Set empty relation
        $statistic->setRelation('targetStatistics', new EloquentCollection([]));

        /** @var TargetStatisticProcessingServiceContract|MockInterface $processingService */
        $processingService = Mockery::mock(TargetStatisticProcessingServiceContract::class);
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