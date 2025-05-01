<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Collection;

use App\Athenia\Contracts\Models\CanBeAggregatedContract;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tests\TestCase;

final class CollectionItemTest extends TestCase
{
    public function testImplementsCanBeAggregatedContract(): void
    {
        $model = new CollectionItem();
        $this->assertInstanceOf(CanBeAggregatedContract::class, $model);
    }

    public function testItem(): void
    {
        $model = new CollectionItem();
        $relation = $model->item();

        $this->assertInstanceOf(MorphTo::class, $relation);
    }

    public function testCategories(): void
    {
        $model = new CollectionItem();
        $relation = $model->categories();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('collection_item_categories', $relation->getTable());
    }

    public function testCollection(): void
    {
        $model = new CollectionItem();
        $relation = $model->collection();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Collection::class, $relation->getRelated());
    }
}