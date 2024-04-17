<?php
declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface CanReceiveTextMessagesContract
 * @package App\Contracts\Models
 * @property $id The primary id of this model
 */
interface CanReceiveTextMessagesContract extends CanBeMorphedTo
{
    /**
     * Gets the formatted phone number to send via twilio
     *
     * @return string|null
     */
    public function routeNotificationForTwilio(): ?string;
}
