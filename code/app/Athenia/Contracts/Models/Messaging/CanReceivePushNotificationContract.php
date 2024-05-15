<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models\Messaging;

use App\Models\Messaging\PushNotificationKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Collection|PushNotificationKey[] $pushNotificationKeys
 */
interface CanReceivePushNotificationContract extends CanReceiveMessageContract
{
    /**
     * The push notification keys that the push notification should be sent to
     *
     * @return HasMany
     */
    public function pushNotificationKeys(): HasMany;
}