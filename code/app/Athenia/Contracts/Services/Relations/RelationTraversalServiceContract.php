<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface RelationTraversalServiceContract
 * @package App\Athenia\Contracts\Services\Relations
 */
interface RelationTraversalServiceContract
{
    /**
     * Traverses through a chain of relations starting from a model and returns all models at the end of the chain
     *
     * @param Model $startingModel The model to start traversing from
     * @param string $relationPath The dot-notation path of relations to traverse (e.g. "parent.children.items")
     * @return Collection The collection of models at the end of the relation chain
     */
    public function traverseRelations(Model $startingModel, string $relationPath): Collection;
} 