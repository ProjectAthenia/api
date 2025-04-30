<?php
declare(strict_types=1);

namespace App\Athenia\Models\Statistics;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Athenia\Models\User\UserStatistic;
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
 * @property-read Collection|UserStatistic[] $userStatistics
 * @property-read int|null $user_statistics_count
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
     * all instances of the user statistics in the system
     *
     * @return HasMany
     */
    public function userStatistics(): HasMany
    {
        return $this->hasMany(UserStatistic::class);
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
                'type' => [
                    'string',
                    Rule::in(['user', 'content', 'interaction']), // Customize these types based on your needs
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
                    'required',
                    'string',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'type',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'type',
                ],
            ],
        ];
    }
} 