<?php
declare(strict_types=1);

namespace App\Athenia\Jobs\Statistics;

use App\Athenia\Contracts\Services\Statistics\TargetStatisticProcessingServiceContract;
use App\Models\Statistics\Statistic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecountStatisticJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Statistic $statistic
     */
    public function __construct(
        private readonly Statistic $statistic
    ) {
    }

    /**
     * Execute the job.
     *
     * @param TargetStatisticProcessingServiceContract $processingService
     * @return void
     */
    public function handle(TargetStatisticProcessingServiceContract $processingService): void
    {
        foreach ($this->statistic->targetStatistics as $targetStatistic) {
            $processingService->processSingleTargetStatistic($targetStatistic);
        }
    }
} 