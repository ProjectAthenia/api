<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Services\Messaging\BaseMessageSendingServiceContract;
use App\Athenia\Contracts\Services\Messaging\MessageSendingSelectionServiceContract;

class MessageSendingSelectionService implements MessageSendingSelectionServiceContract
{
    /**
     * @param array|BaseMessageSendingServiceContract[] $services All services that are enabled
     */
    public function __construct(private array $services) {}

    /**
     * Gets the service based on the passed in name
     *
     * @param string $name
     * @return BaseMessageSendingServiceContract|null
     */
    public function getSendingService(string $name): ?BaseMessageSendingServiceContract
    {
        return $this->services[$name] ?? null;
    }
}