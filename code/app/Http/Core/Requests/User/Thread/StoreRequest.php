<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\User\Thread;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\Thread;
use App\Policies\User\ThreadPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\User\Thread
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
        return ThreadPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Thread::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        $subjectType = $this->input('subject_type', '');
        $subjectId = $this->input('subject_id', null);

        return [
            $this->route('user'),
            $subjectType,
            $subjectId,
        ];
    }

    /**
     * The rules for the request
     *
     * @param Thread $thread
     * @return array
     */
    public function rules(Thread $thread)
    {
        return $thread->getValidationRules(Thread::VALIDATION_RULES_CREATE);
    }
}