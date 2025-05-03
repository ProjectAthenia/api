<?php
declare(strict_types=1);

namespace App\Athenia\Services\Relations;

use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RelationTraversalService
 * @package App\Athenia\Services\Relations
 */
class RelationTraversalService implements RelationTraversalServiceContract
{
    /**
     * Traverses through a chain of relations starting from a model and returns all models at the end of the chain
     *
     * @param Model $startingModel The model to start traversing from
     * @param string $relationPath The dot-notation path of relations to traverse (e.g. "parent.children.items")
     * @return Collection The collection of models at the end of the relation chain
     */
    public function traverseRelations(Model $startingModel, string $relationPath): Collection
    {
        $currentModels = new Collection([$startingModel]);

        if (empty($relationPath)) {
            return $currentModels;
        }

        $relations = explode('.', $relationPath);

        foreach ($relations as $relation) {
            $nextModels = new Collection();
            
            foreach ($currentModels as $model) {
                // Load the relation if it hasn't been loaded
                if (!$model->relationLoaded($relation)) {
                    $model->load($relation);
                }
                
                $related = $model->{$relation};
                
                // Handle both single models and collections
                if ($related instanceof Collection) {
                    foreach ($related as $relatedModel) {
                        $nextModels->push($relatedModel);
                    }
                } elseif ($related instanceof Model) {
                    $nextModels->push($related);
                }
            }
            
            $currentModels = $nextModels;
        }

        return $currentModels;
    }
} 