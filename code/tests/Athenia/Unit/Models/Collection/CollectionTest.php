<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Collection;

use App\Models\Collection\Collection;
use Tests\TestCase;

final class CollectionTest extends TestCase
{
    public function testCollectionItems(): void
    {
        $model = new Collection();
        $relation = $model->collectionItems();

        $this->assertEquals('collections.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('collection_items.collection_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOwner(): void
    {
        $model = new Collection();
        $relation = $model->owner();

        $this->assertEquals('collections.owner_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('owner_type', $relation->getMorphType());
    }

    public function testTargetStatisticsRelation(): void
    {
        $model = new \App\Models\Collection\Collection();
        $relation = $model->targetStatistics();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $relation);
        $this->assertEquals('target_type', $relation->getMorphType());
        $this->assertEquals('target_id', $relation->getForeignKeyName());
        $this->assertEquals(\App\Models\Statistic\TargetStatistic::class, get_class($relation->getRelated()));
    }
}