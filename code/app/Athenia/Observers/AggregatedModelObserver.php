<?php
declare(strict_types=1);

namespace App\Athenia\Observers;

use App\Athenia\Contracts\Models\CanBeAggregatedContract;
use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Athenia\Jobs\Statistics\ProcessTargetStatisticsJob;
use Illuminate\Contracts\Bus\Dispatcher;

class AggregatedModelObserver
{
    public function __construct(
        private readonly RelationTraversalServiceContract $relationTraversalService,
        private readonly Dispatcher $dispatcher
    ) {}

    /**
     * Handle the Model "created" event.
     */
    public function created(CanBeAggregatedContract $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(CanBeAggregatedContract $model): void
    {
        $this->dispatchStatisticProcessing($model);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(CanBeAggregatedContract $model): void
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
     * Dispatches statistic processing for models that can be aggregated
     */
    private function dispatchStatisticProcessing(CanBeAggregatedContract $model): void
    {
        foreach ($model->getStatisticTargetRelationPath() as $relationPath) {
            $targetModels = $this->relationTraversalService->traverseRelations($model, $relationPath);
            
            foreach ($targetModels as $targetModel) {
                if ($targetModel instanceof CanBeStatisticTargetContract) {
                    $this->dispatcher->dispatch(new ProcessTargetStatisticsJob($targetModel));
                }
            }
        }
    }
} 