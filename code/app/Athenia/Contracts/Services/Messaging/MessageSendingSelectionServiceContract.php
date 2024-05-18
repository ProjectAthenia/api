<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Messaging;

interface MessageSendingSelectionServiceContract
{
    /**
     * Gets the service based on the passed in name
     *
     * @param string $name
     * @return BaseMessageSendingServiceContract|null
     */
    public function getSendingService(string $name): ?BaseMessageSendingServiceContract;
}