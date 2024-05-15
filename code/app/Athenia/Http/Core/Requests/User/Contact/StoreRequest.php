<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\Contact;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\Contact;
use App\Policies\User\ContactPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\User\Contact
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ContactPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Contact::class;
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
     * @param Contact $model
     * @return array
     */
    public function rules(Contact $model)
    {
        return $model->getValidationRules(Contact::VALIDATION_RULES_CREATE);
    }
}