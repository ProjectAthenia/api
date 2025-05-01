<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistics;

use App\Athenia\Contracts\Services\Statistics\StatisticRelationTraversalServiceContract;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class TargetStatisticProcessingService
 * @package App\Athenia\Services\Statistics
 */
class TargetStatisticProcessingService
{
    /**
     * @var StatisticRelationTraversalServiceContract
     */
    private StatisticRelationTraversalServiceContract $relationTraversalService;

    /**
     * TargetStatisticProcessingService constructor.
     * @param StatisticRelationTraversalServiceContract $relationTraversalService
     */
    public function __construct(StatisticRelationTraversalServiceContract $relationTraversalService)
    {
        $this->relationTraversalService = $relationTraversalService;
    }

    /**
     * Processes a target statistic by traversing relations and applying filters
     *
     * @param TargetStatistic $targetStatistic
     * @return array
     */
    public function processTargetStatistic(TargetStatistic $targetStatistic): array
    {
        // Get all models at the end of the relation chain
        $models = $this->relationTraversalService->getRelatedModels(
            $targetStatistic->statistic,
            $targetStatistic->target
        );

        // Get all filters for this statistic
        $filters = $targetStatistic->statistic->filters;

        // Apply filters to the models
        $filteredModels = $this->applyFilters($models, $filters);

        // Check if any filter requires unique values
        $uniqueFilter = $filters->first(function (StatisticFilter $filter) {
            return $filter->operator === 'unique';
        });

        // Process results based on whether we need unique values or a total count
        if ($uniqueFilter) {
            return $this->processUniqueResults($filteredModels, $uniqueFilter);
        }

        return ['total' => $filteredModels->count()];
    }

    /**
     * Applies all filters to the collection of models
     *
     * @param Collection $models
     * @param Collection $filters
     * @return Collection
     */
    private function applyFilters(Collection $models, Collection $filters): Collection
    {
        return $models->filter(function ($model) use ($filters) {
            foreach ($filters as $filter) {
                if ($filter->operator === 'unique') {
                    continue;
                }

                $fieldValue = data_get($model, $filter->field);
                $filterValue = $filter->value;

                if (!$this->evaluateFilter($fieldValue, $filter->operator, $filterValue)) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Evaluates a single filter condition
     *
     * @param mixed $fieldValue
     * @param string $operator
     * @param mixed $filterValue
     * @return bool
     */
    private function evaluateFilter($fieldValue, string $operator, $filterValue): bool
    {
        switch ($operator) {
            case '=':
                return $fieldValue == $filterValue;
            case '!=':
                return $fieldValue != $filterValue;
            case '>':
                return $fieldValue > $filterValue;
            case '>=':
                return $fieldValue >= $filterValue;
            case '<':
                return $fieldValue < $filterValue;
            case '<=':
                return $fieldValue <= $filterValue;
            case 'in':
                return in_array($fieldValue, explode(',', $filterValue));
            case 'not in':
                return !in_array($fieldValue, explode(',', $filterValue));
            case 'like':
                return str_contains(strtolower($fieldValue), strtolower($filterValue));
            case 'not like':
                return !str_contains(strtolower($fieldValue), strtolower($filterValue));
            default:
                return true;
        }
    }

    /**
     * Processes results for unique value grouping
     *
     * @param Collection $models
     * @param StatisticFilter $uniqueFilter
     * @return array
     */
    private function processUniqueResults(Collection $models, StatisticFilter $uniqueFilter): array
    {
        $results = [];
        
        // Group models by the unique field value
        $groupedModels = $models->groupBy(function ($model) use ($uniqueFilter) {
            return data_get($model, $uniqueFilter->field);
        });

        // Count models in each group
        foreach ($groupedModels as $value => $group) {
            $results[$value] = $group->count();
        }

        return $results;
    }
} 