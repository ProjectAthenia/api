<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\Contact;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\Contact;
use App\Policies\User\ContactPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\User\Contact
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ContactPolicy::ACTION_UPDATE;
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
            $this->route('contact'),
        ];
    }

    /**
     * The rules for this request
     *
     * @param Contact $model
     */
    public function rules(Contact $model)
    {
        return $model->getValidationRules(Contact::VALIDATION_RULES_UPDATE);
    }
}