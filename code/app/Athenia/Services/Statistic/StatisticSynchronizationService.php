<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistic;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Repositories\Statistic\StatisticRepositoryContract;
use App\Athenia\Contracts\Repositories\Statistic\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Statistic\StatisticSynchronizationServiceContract;
use App\Models\Statistic\Statistic;
use App\Models\Statistic\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Class StatisticSynchronizationService
 * @package App\Athenia\Services\Statistics
 */
class StatisticSynchronizationService implements StatisticSynchronizationServiceContract
{
    public function __construct(
        private readonly StatisticRepositoryContract $statisticRepository,
        private readonly TargetStatisticRepositoryContract $targetStatisticRepository
    ) {
    }

    /**
     * Takes in a model that can be a statistic target, and ensures that all necessary target
     * statistics exist for that model based on the available statistics for its type
     *
     * @param CanBeStatisticTargetContract $model
     * @return Collection|TargetStatistic[]
     */
    public function synchronizeTargetStatistics(CanBeStatisticTargetContract $model): Collection
    {
        $existingTargetStatistics = $model->targetStatistics ?? new Collection();
        $statistics = $this->statisticRepository->findAll(['model' => $model->morphRelationName()]);
        $newTargetStatistics = new Collection();

        foreach ($statistics as $statistic) {
            if (!$existingTargetStatistics->contains('statistic_id', $statistic->id)) {
                $newTargetStatistic = $this->targetStatisticRepository->create([
                    'statistic_id' => $statistic->id,
                    'target_id' => $model->id,
                    'target_type' => $model->morphRelationName(),
                ]);

                $newTargetStatistics->push($newTargetStatistic);
            }
        }

        // Create a new Eloquent Collection with all items
        $allItems = array_merge($existingTargetStatistics->all(), $newTargetStatistics->all());
        return new Collection($allItems);
    }

    /**
     * Creates target statistics for all models that should have them for the given statistic
     *
     * @param Statistic $statistic
     * @return Collection
     */
    public function createTargetStatisticsForStatistic(Statistic $statistic): Collection
    {
        $targetStatistics = new Collection();
        $models = $this->getModelsForStatistic($statistic);

        foreach ($models as $model) {
            // Check if a target statistic already exists for this model and statistic
            $existingTargetStatistic = $this->targetStatisticRepository->findForTarget($model, $statistic->id);

            if (!$existingTargetStatistic) {
                $targetStatistic = $this->targetStatisticRepository->create([
                    'statistic_id' => $statistic->id,
                    'target_id' => $model->id,
                    'target_type' => $model->morphRelationName(),
                ]);

                $targetStatistics->push($targetStatistic);
            } else {
                $targetStatistics->push($existingTargetStatistic);
            }
        }

        return $targetStatistics;
    }

    /**
     * Get all models that should have target statistics for the given statistic.
     *
     * @param Statistic $statistic
     * @return CanBeStatisticTargetContract[]
     */
    private function getModelsForStatistic(Statistic $statistic): array
    {
        // Get the model class from Laravel's morph map
        $modelClass = Relation::getMorphedModel($statistic->model);
        
        if (!$modelClass) {
            throw new \RuntimeException("No morph map found for model type: {$statistic->model}");
        }
        
        // Build and execute the query using the model's query builder
        return $modelClass::query()->get()->all();
    }
} 