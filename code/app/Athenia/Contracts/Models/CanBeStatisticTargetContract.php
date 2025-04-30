<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models;

use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface CanBeStatisticTargetContract
 * @package App\Athenia\Contracts\Models
 */
interface CanBeStatisticTargetContract extends CanBeMorphedTo
{
    /**
     * Gets all statistics that belong to this model through a morph many relationship
     *
     * @return MorphMany|TargetStatistic[]
     */
    public function targetStatistics(): MorphMany;
} 