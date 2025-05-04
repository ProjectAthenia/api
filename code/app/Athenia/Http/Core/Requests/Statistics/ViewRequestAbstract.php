<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Statistics;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Statistics\Statistic;
use App\Athenia\Policies\Statistics\StatisticPolicy;

/**
 * Class ViewRequestAbstract
 * @package App\Athenia\Http\Core\Requests\Statistics
 */
abstract class ViewRequestAbstract extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return StatisticPolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Statistic::class;
    }

    /**
     * @inheritDoc
     */
    public function allowedExpands(): array
    {
        return [
            'statisticFilters',
        ];
    }
} 