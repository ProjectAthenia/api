<?php
declare(strict_types=1);

namespace App\Athenia\Services\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Athenia\Contracts\Services\Relations\RelationTraversalServiceContract;
use App\Models\Statistics\StatisticFilter;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class SingleTargetStatisticProcessingService
{
    private RelationTraversalServiceContract $relationTraversalService;
    private TargetStatisticRepositoryContract $targetStatisticRepository;

    public function __construct(
        RelationTraversalServiceContract $relationTraversalService,
        TargetStatisticRepositoryContract $targetStatisticRepository
    ) {
        $this->relationTraversalService = $relationTraversalService;
        $this->targetStatisticRepository = $targetStatisticRepository;
    }

    public function processSingleTargetStatistic(TargetStatistic $targetStatistic): void
    {
        // Get all models at the end of the relation chain
        $models = $this->relationTraversalService->traverseRelations(
            $targetStatistic->target,
            $targetStatistic->statistic->relation
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
        $result = $uniqueFilter 
            ? $this->processUniqueResults($filteredModels, $uniqueFilter)
            : ['total' => $filteredModels->count()];

        // Update the target statistic through the repository
        $this->targetStatisticRepository->update($targetStatistic, ['result' => $result]);
    }

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
            default:
                return false;
        }
    }

    private function processUniqueResults(Collection $models, StatisticFilter $uniqueFilter): array
    {
        $uniqueValues = $models->pluck($uniqueFilter->field)->unique();
        $result = [];
        
        foreach ($uniqueValues as $value) {
            $result[$value] = $models->where($uniqueFilter->field, $value)->count();
        }
        
        return $result;
    }
} 