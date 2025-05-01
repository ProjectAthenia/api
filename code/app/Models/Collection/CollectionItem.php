<?php
declare(strict_types=1);

namespace App\Models\Collection;

use App\Athenia\Contracts\Models\CanBeAggregatedContract;
use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\Rule;

/**
 * App\Models\Collection\CollectionItem
 *
 * @property int $id
 * @property int $item_id
 * @property string $item_type
 * @property int $collection_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Collection\Collection $collection
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
 * @method static \Database\Factories\Collection\CollectionItemFactory factory(...$parameters)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem getAggregateMethod()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isAppendRelationsCount()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isLeftJoin()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isUseTableAlias()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem joinRelations($relations, $leftJoin = null)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem onlyTrashed()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereInJoin($column, $values)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereJoin($column, $operator, $value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereNotInJoin($column, $values)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orderByJoin($column, $direction = 'asc', $aggregateMethod = null)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem query()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setAggregateMethod(string $aggregateMethod)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setAppendRelationsCount(bool $appendRelationsCount)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setLeftJoin(bool $leftJoin)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setUseTableAlias(bool $useTableAlias)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereCollectionId($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereCreatedAt($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereDeletedAt($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereId($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereInJoin($column, $values, $boolean = 'and', $not = false)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereItemId($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereItemType($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereJoin($column, $operator, $value, $boolean = 'and')
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereNotInJoin($column, $values, $boolean = 'and')
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereOrder($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem withoutTrashed()
 * @mixin \Eloquent
 */
class CollectionItem extends BaseModelAbstract implements HasValidationRulesContract, CanBeAggregatedContract
{
    use HasValidationRules;

    /**
     * The item this is related to
     *
     * @return MorphTo
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * All categories for this collection item
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'collection_item_categories');
    }

    /**
     * The collection this item is apart of
     *
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * @param ...$params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'item_id' => [
                    'integer',
                ],
                'item_type' => [
                    Rule::in(['article']),
                ],
                'order' => [
                    'integer',
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => ['item_id', 'item_type', 'order'],
            ]
        ];
    }
}