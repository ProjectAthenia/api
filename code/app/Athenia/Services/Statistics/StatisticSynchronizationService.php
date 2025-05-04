<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Statistics\StatisticSynchronizationServiceContract;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class StatisticSynchronizationService
 * @package App\Athenia\Services\Statistics
 */
class StatisticSynchronizationService implements StatisticSynchronizationServiceContract
{
    /**
     * @var StatisticRepositoryContract
     */
    private StatisticRepositoryContract $statisticRepository;

    /**
     * @var TargetStatisticRepositoryContract
     */
    private TargetStatisticRepositoryContract $targetStatisticRepository;

    /**
     * StatisticSynchronizationService constructor.
     * @param StatisticRepositoryContract $statisticRepository
     * @param TargetStatisticRepositoryContract $targetStatisticRepository
     */
    public function __construct(
        StatisticRepositoryContract $statisticRepository,
        TargetStatisticRepositoryContract $targetStatisticRepository
    ) {
        $this->statisticRepository = $statisticRepository;
        $this->targetStatisticRepository = $targetStatisticRepository;
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
        // Get the morph type for this model
        $morphType = $model->morphRelationName();
        
        // Load all statistics that apply to this model type
        $statistics = $this->statisticRepository->findAll([
            'model' => $morphType,
        ]);

        // Load all existing target statistics for this model
        $existingTargetStatistics = $model->targetStatistics;
        
        // Create a map of existing target statistics by statistic ID for easy lookup
        $existingTargetStatisticMap = $existingTargetStatistics->keyBy('statistic_id');
        
        // Create any missing target statistics
        $newTargetStatistics = new BaseCollection();
        foreach ($statistics as $statistic) {
            if (!$existingTargetStatisticMap->has($statistic->id)) {
                $newTargetStatistics->push(
                    $this->targetStatisticRepository->create([
                        'statistic_id' => $statistic->id,
                        'target_id' => $model->id,
                        'target_type' => $morphType,
                    ])
                );
            }
        }
        
        // Merge existing and new target statistics and return
        return new Collection($existingTargetStatistics->concat($newTargetStatistics));
    }
} 