<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Collection;

use App\Models\Collection\CollectionItem;
use Tests\TestCase;

final class CollectionItemTest extends TestCase
{
    public function testItem(): void
    {
        $model = new CollectionItem();
        $relation = $model->item();

        $this->assertEquals('collection_items.item_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('item_type', $relation->getMorphType());
    }

    public function testCategories(): void
    {
        $model = new CollectionItem();
        $relation = $model->categories();

        $this->assertEquals('collection_item_categories', $relation->getTable());
        $this->assertEquals('collection_item_categories.collection_item_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('collection_item_categories.category_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertEquals('collection_items.id', $relation->getQualifiedParentKeyName());
    }

    public function testCollection(): void
    {
        $model = new CollectionItem();
        $relation = $model->collection();

        $this->assertEquals('collections.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('collection_items.collection_id', $relation->getQualifiedForeignKeyName());
    }
}