<?php
declare(strict_types=1);

namespace App\Athenia\Observers;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Jobs\Statistics\ProcessTargetStatisticsJob;
use Illuminate\Database\Eloquent\Model;

class AggregatedModelObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Dispatches statistic processing for models that can be statistic targets
     */
    private function dispatchStatisticProcessing(Model $model): void
    {
        if ($model instanceof CanBeStatisticTargetContract) {
            ProcessTargetStatisticsJob::dispatch($model);
        }
    }
} 