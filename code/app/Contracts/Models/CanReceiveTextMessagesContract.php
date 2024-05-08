<?php
declare(strict_types=1);

namespace App\Contracts\Models\Messaging;

/**
 * Interface CanReceiveTextMessagesContract
 * @package App\Contracts\Models
 */
interface CanReceiveTextMessagesContract extends CanReceiveMessageContract
{
    /**
     * Gets the formatted phone number to send via twilio
     *
     * @return string|null
     */
    public function routeNotificationForTwilio(): ?string;
}
