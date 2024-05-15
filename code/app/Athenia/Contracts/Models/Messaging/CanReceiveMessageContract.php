<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models\Messaging;

use App\Athenia\Contracts\Models\CanBeMorphedToContract;
use App\Models\Messaging\Message;

/**
 * @property $id The primary id of this model
 */
interface CanReceiveMessageContract extends CanBeMorphedToContract
{
    /**
     * This will return if the message can be received by the specific model
     *
     * @param Message $message
     * @return bool
     */
    public function canReceive(Message $message): bool;
}