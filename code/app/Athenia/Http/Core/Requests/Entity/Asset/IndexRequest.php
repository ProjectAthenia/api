<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\Asset;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Asset;
use App\Policies\AssetPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Entity\Asset
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract implements HasEntityInRequestContract
{
    use HasNoRules, HasNoExpands, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return AssetPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Asset::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->getEntity(),
        ];
    }
}