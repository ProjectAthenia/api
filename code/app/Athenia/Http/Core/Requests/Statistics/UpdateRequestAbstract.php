<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Statistics;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Athenia\Models\Statistics\Statistic;
use App\Athenia\Policies\Statistics\StatisticPolicy;

/**
 * Class UpdateRequestAbstract
 * @package App\Athenia\Http\Core\Requests\Statistics
 */
abstract class UpdateRequestAbstract extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return StatisticPolicy::ACTION_UPDATE;
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
     * The rules needed for the request
     *
     * @param Statistic $statistic
     * @return array
     */
    public function rules(Statistic $statistic): array
    {
        return $statistic->getValidationRules(Statistic::VALIDATION_RULES_UPDATE);
    }
} 