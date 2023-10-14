<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Traits\HasValidationRules;
use Illuminate\Database\Query\Builder;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category getAggregateMethod()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category isAppendRelationsCount()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category isLeftJoin()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category isUseTableAlias()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category joinRelations($relations, $leftJoin = null)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category onlyTrashed()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category orWhereInJoin($column, $values)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category orWhereJoin($column, $operator, $value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category orWhereNotInJoin($column, $values)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category orderByJoin($column, $direction = 'asc', $aggregateMethod = null)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category query()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category setAggregateMethod(string $aggregateMethod)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category setAppendRelationsCount(bool $appendRelationsCount)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category setLeftJoin(bool $leftJoin)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category setUseTableAlias(bool $useTableAlias)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereCreatedAt($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereDeletedAt($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereDescription($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereId($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereInJoin($column, $values, $boolean = 'and', $not = false)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereJoin($column, $operator, $value, $boolean = 'and')
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereName($value)
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereNotInJoin($column, $values, $boolean = 'and')
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category withoutTrashed()
 * @mixin \Eloquent
 */
class Category extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * Makes sure everything is by default ordered by the name
     *
     * @return Builder
     */
    public function newQuery()
    {
        $query = parent::newQuery();

        $query->orderBy('name');

        return $query;
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                ],
            ],
        ];
    }
}
