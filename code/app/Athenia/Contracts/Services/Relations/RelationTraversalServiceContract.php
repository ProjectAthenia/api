<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Relations;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface RelationTraversalServiceContract
 * @package App\Athenia\Contracts\Services\Relations
 */
interface RelationTraversalServiceContract
{
    /**
     * Traverses the relations on a model and returns all related models
     *
     * @param BaseModelAbstract $model
     * @param string $relationPath
     * @return Collection
     */
    public function traverseRelations(BaseModelAbstract $model, string $relationPath): Collection;
} 