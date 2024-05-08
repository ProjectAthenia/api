<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\User\Thread\Message;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Messaging\Message;
use App\Policies\Messaging\MessagePolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\User\Thread\Message
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
            $this->route('message'),
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
        return $message->getValidationRules(Message::VALIDATION_RULES_UPDATE);
    }
}