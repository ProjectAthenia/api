<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Collection;

use App\Models\Collection\Collection;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCollectionItems()
    {
        $model = new Collection();
        $relation = $model->collectionItems();

        $this->assertEquals('collections.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('collection_items.collection_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOwner()
    {
        $model = new Collection();
        $relation = $model->owner();

        $this->assertEquals('collections.owner_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('owner_type', $relation->getMorphType());
    }
}