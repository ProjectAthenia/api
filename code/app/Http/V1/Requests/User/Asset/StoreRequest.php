<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\User\Asset;

use App\Http\V1\Requests\BaseAssetUploadRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoExpands;
use App\Models\Asset;
use App\Policies\AssetPolicy;

/**
 * Class StoreRequest
 * @package App\Http\V1\Requests\User\Asset
 */
class StoreRequest extends BaseAssetUploadRequestAbstract
{
    use HasNoExpands;

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
            $this->route('user'),
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