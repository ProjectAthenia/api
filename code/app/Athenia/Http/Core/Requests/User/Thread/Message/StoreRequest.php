<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\User\Thread\Message;

use App\Athenia\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Messaging\Message;
use App\Policies\Messaging\MessagePolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\User\Thread\Message
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
        return MessagePolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Message::class;
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
            $this->route('thread'),
        ];
    }

    /**
     * The rules for the request
     *
     * @param Message $message
     * @return array
     */
    public function rules(Message $message)
    {
        return $message->getValidationRules(Message::VALIDATION_RULES_CREATE);
    }
}