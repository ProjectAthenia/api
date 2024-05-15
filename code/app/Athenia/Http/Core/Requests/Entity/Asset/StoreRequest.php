<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\Asset;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Asset;
use App\Policies\AssetPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Entity\Asset
 */
class StoreRequest extends BaseAssetUploadRequestAbstract implements HasEntityInRequestContract
{
    use HasNoExpands, IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return AssetPolicy::ACTION_CREATE;
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

    /**
     * @param Asset $model
     * @return array
     */
    public function rules(Asset $model)
    {
        return $model->getValidationRules(Asset::VALIDATION_RULES_CREATE);
    }
}