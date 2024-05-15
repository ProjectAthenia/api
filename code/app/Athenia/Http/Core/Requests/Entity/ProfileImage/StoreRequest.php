<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Entity\ProfileImage;

use App\Athenia\Contracts\Http\HasEntityInRequestContract;
use App\Athenia\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use App\Athenia\Http\Core\Requests\Entity\Traits\IsEntityRequestTrait;
use App\Models\User\ProfileImage;
use App\Policies\User\ProfileImagePolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Entity\ProfileImage
 */
class StoreRequest extends BaseAssetUploadRequestAbstract implements HasEntityInRequestContract
{
    use IsEntityRequestTrait;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ProfileImagePolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ProfileImage::class;
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
     * @param ProfileImage $profileImage
     * @return array
     */
    public function rules(ProfileImage $profileImage): array
    {
        return $profileImage->getValidationRules(ProfileImage::VALIDATION_RULES_CREATE);
    }
}