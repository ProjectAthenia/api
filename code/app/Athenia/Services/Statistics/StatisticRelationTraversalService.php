<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Contracts\Services\Statistics\StatisticRelationTraversalServiceContract;
use App\Models\Statistics\Statistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StatisticRelationTraversalService
 * @package App\Athenia\Services\Statistics
 */
class StatisticRelationTraversalService implements StatisticRelationTraversalServiceContract
{
    /**
     * Takes a statistic and a target, and traverses through the relation chain specified
     * in the statistic to return all models at the end of the relation chain
     *
     * @param Statistic $statistic The statistic containing the relation path
     * @param CanBeStatisticTargetContract&Model $target The model to start traversing from
     * @return Collection The collection of models at the end of the relation chain
     */
    public function getRelatedModels(Statistic $statistic, CanBeStatisticTargetContract $target): Collection
    {
        $relationPath = $statistic->relation;
        $currentModels = collect([$target]);

        if (empty($relationPath)) {
            return $currentModels;
        }

        $relations = explode('.', $relationPath);

        foreach ($relations as $relation) {
            $nextModels = collect();
            
            foreach ($currentModels as $model) {
                // Load the relation if it hasn't been loaded
                if (!$model->relationLoaded($relation)) {
                    $model->load($relation);
                }
                
                $related = $model->{$relation};
                
                // Handle both single models and collections
                if ($related instanceof Collection) {
                    $nextModels = $nextModels->concat($related);
                } elseif ($related instanceof Model) {
                    $nextModels->push($related);
                }
            }
            
            $currentModels = $nextModels;
        }

        return $currentModels;
    }
} 