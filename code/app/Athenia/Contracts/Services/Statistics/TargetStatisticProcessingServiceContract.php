<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Statistics;

use App\Models\Statistics\TargetStatistic;

/**
 * Interface TargetStatisticProcessingServiceContract
 * @package App\Athenia\Contracts\Services\Statistics
 */
interface TargetStatisticProcessingServiceContract
{
    /**
     * Processes a single target statistic and updates its result
     *
     * @param TargetStatistic $targetStatistic
     * @return void
     */
    public function processSingleTargetStatistic(TargetStatistic $targetStatistic): void;
} 