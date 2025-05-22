<?php
declare(strict_types=1);

namespace App\Athenia\Models\Traits;

use App\Models\Statistic\TargetStatistic;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasStatisticTargets
 * @package App\Athenia\Models\Traits
 */
trait HasStatisticTargets
{
    /**
     * Gets all statistics that belong to this model through a morph many relationship
     *
     * @return MorphMany|TargetStatistic[]
     */
    public function targetStatistics(): MorphMany
    {
        return $this->morphMany(TargetStatistic::class, 'target');
    }
} 