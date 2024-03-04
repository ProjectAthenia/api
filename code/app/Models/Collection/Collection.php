<?php
declare(strict_types=1);

namespace App\Models\Collection;

use App\Contracts\Models\HasValidationRulesContract;
use App\Contracts\Models\IsAnEntity;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use App\Validators\OwnedByValidator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\Rule;

/**
 * App\Models\Collection\Collection
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string|null $name
 * @property int $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int|null $collection_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Collection\CollectionItem> $collectionItems
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @method static \Database\Factories\Collection\CollectionFactory factory(...$parameters)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection getAggregateMethod()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection isAppendRelationsCount()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection isLeftJoin()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection isUseTableAlias()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection joinRelations($relations, $leftJoin = null)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection onlyTrashed()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection orWhereInJoin($column, $values)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection orWhereJoin($column, $operator, $value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection orWhereNotInJoin($column, $values)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection orderByJoin($column, $direction = 'asc', $aggregateMethod = null)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection query()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection setAggregateMethod(string $aggregateMethod)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection setAppendRelationsCount(bool $appendRelationsCount)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection setLeftJoin(bool $leftJoin)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection setUseTableAlias(bool $useTableAlias)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereCollectionItemsCount($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereCreatedAt($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereDeletedAt($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereId($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereInJoin($column, $values, $boolean = 'and', $not = false)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereIsPublic($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereJoin($column, $operator, $value, $boolean = 'and')
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereName($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereNotInJoin($column, $values, $boolean = 'and')
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereOwnerId($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereOwnerType($value)
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|Collection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withoutTrashed()
 * @mixin \Eloquent
 */
class Collection extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * All collection items
     *
     * @return HasMany
     */
    public function collectionItems(): HasMany
    {
        return $this->hasMany(CollectionItem::class)
            ->orderBy('order');
    }

    /**
     * The owner
     *
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param ...$params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'name' => [
                    'nullable',
                    'string',
                ],
                'is_public' => [
                    'boolean',
                ],
                'collection_item_order' => [
                    'array',
                ],
                'collection_item_order.*' => [
                    'integer',
                    Rule::exists('collection_items', 'id'),
                    OwnedByValidator::KEY . ':collection,collectionItems',
                ]
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => ['is_public'],
                static::VALIDATION_PREPEND_NOT_PRESENT => ['collection_item_order'],
            ],
        ];
    }
}