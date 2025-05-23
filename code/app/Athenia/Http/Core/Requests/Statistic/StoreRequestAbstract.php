<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Statistic;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Statistic\Statistic;
use App\Policies\Statistic\StatisticPolicy;

/**
 * Class StoreRequestAbstract
 * @package App\Athenia\Http\Core\Requests\Statistic
 */
abstract class StoreRequestAbstract extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return StatisticPolicy::ACTION_CREATE;
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
        return $statistic->getValidationRules(Statistic::VALIDATION_RULES_CREATE);
    }
} 