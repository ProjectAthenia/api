<?php
declare(strict_types=1);

namespace App\Models\Statistics;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Models\Statistics\TargetStatistic;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class Statistic
 *
 * @property int $id
 * @property string $type
 * @property int $total
 * @property Carbon|null $deleted_at
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property string|null $name
 * @property bool $public
 * @property-read Collection|StatisticFilter[] $statisticFilters
 * @property-read int|null $statistic_filters_count
 * @property-read Collection|TargetStatistic[] $targetStatistics
 * @property-read int|null $target_statistics_count
 * @mixin Eloquent
 */
class Statistic extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The filters that we use to determine what to count
     *
     * @return HasMany
     */
    public function statisticFilters(): HasMany
    {
        return $this->hasMany(StatisticFilter::class);
    }

    /**
     * All instances of the target statistics in the system
     *
     * @return HasMany
     */
    public function targetStatistics(): HasMany
    {
        return $this->hasMany(TargetStatistic::class);
    }

    /**
     * @inheritDoc
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                ],
                'model' => [
                    'string',
                ],
                'relation' => [
                    'string',
                ],
                'public' => [
                    'boolean',
                ],
                'statistic_filters' => [
                    'array',
                ],
                'statistic_filters.*' => [
                    'array',
                ],
                'statistic_filters.*.field' => [
                    'required',
                    'string',
                ],
                'statistic_filters.*.operator' => [
                    'required',
                    'string',
                ],
                'statistic_filters.*.value' => [
                    'nullable',
                    'string',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                    'model',
                    'relation',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'model',
                    'relation',
                ],
            ],
        ];
    }
} 