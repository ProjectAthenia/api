<?php
declare(strict_types=1);

namespace App\Contracts\Models\Messaging;

interface CanReceiveSMSContract extends CanReceiveMessageContract
{
    /**
     * Gets the phone number for routing SMS messages
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string;
}