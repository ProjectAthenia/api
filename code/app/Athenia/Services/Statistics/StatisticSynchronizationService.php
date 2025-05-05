<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Statistics\StatisticSynchronizationServiceContract;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\TargetStatistic;
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

    public function createTargetStatisticsForStatistic(Statistic $statistic): Collection
    {
        $targetStatistics = new Collection();
        $models = $this->getModelsForStatistic($statistic);

        foreach ($models as $model) {
            $targetStatistic = $this->targetStatisticRepository->create([
                'statistic_id' => $statistic->id,
                'target_id' => $model->id,
                'target_type' => $model->morphRelationName(),
            ]);

            $targetStatistics->push($targetStatistic);
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