<?php
declare(strict_types=1);

namespace Tests\Mocks;

use App\Athenia\Http\Core\Requests\BaseAssetUploadRequestAbstract;

class AssetUploadRequest extends BaseAssetUploadRequestAbstract
{
    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return "";
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return "";
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [];
    }
}