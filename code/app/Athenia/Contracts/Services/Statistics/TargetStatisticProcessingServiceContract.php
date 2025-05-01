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
     * Processes a target statistic by traversing relations and applying filters
     *
     * @param TargetStatistic $targetStatistic
     * @return array
     */
    public function processTargetStatistic(TargetStatistic $targetStatistic): array;
} 