<?php
declare(strict_types=1);

namespace App\Models\Collection;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Category;
use App\Models\Traits\HasValidationRules;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem getAggregateMethod()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isAppendRelationsCount()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isLeftJoin()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem isUseTableAlias()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem joinRelations($relations, $leftJoin = null)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem onlyTrashed()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereInJoin($column, $values)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereJoin($column, $operator, $value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orWhereNotInJoin($column, $values)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem orderByJoin($column, $direction = 'asc', $aggregateMethod = null)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem query()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setAggregateMethod(string $aggregateMethod)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setAppendRelationsCount(bool $appendRelationsCount)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setLeftJoin(bool $leftJoin)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem setUseTableAlias(bool $useTableAlias)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereCollectionId($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereCreatedAt($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereDeletedAt($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereId($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereInJoin($column, $values, $boolean = 'and', $not = false)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereItemId($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereItemType($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereJoin($column, $operator, $value, $boolean = 'and')
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereNotInJoin($column, $values, $boolean = 'and')
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereOrder($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|CollectionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionItem withoutTrashed()
 * @mixin \Eloquent
 */
class CollectionItem extends BaseModelAbstract implements HasValidationRulesContract
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