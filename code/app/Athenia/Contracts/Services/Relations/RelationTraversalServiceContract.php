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
     * Traverses the relations on a model and returns all related models
     *
     * @param Model $model
     * @param string $relationPath
     * @return Collection
     */
    public function traverseRelations(Model $model, string $relationPath): Collection;
} 