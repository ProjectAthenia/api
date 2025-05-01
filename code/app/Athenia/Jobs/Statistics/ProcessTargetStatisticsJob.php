<?php
declare(strict_types=1);

namespace App\Athenia\Jobs\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Services\Statistics\TargetStatisticProcessingServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTargetStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var CanBeStatisticTargetContract
     */
    private CanBeStatisticTargetContract $target;

    /**
     * Create a new job instance.
     *
     * @param CanBeStatisticTargetContract $target
     */
    public function __construct(CanBeStatisticTargetContract $target)
    {
        $this->target = $target;
    }

    /**
     * Execute the job.
     *
     * @param TargetStatisticProcessingServiceContract $processingService
     * @return void
     */
    public function handle(TargetStatisticProcessingServiceContract $processingService): void
    {
        foreach ($this->target->targetStatistics as $targetStatistic) {
            $result = $processingService->processTargetStatistic($targetStatistic);
            $targetStatistic->update(['result' => $result]);
        }
    }
} 