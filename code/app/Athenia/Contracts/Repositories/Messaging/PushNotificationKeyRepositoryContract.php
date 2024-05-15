<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Messaging;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\Messaging\PushNotificationKey;

/**
 * Interface PushNotificationKeyRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface PushNotificationKeyRepositoryContract extends BaseRepositoryContract
{
    /**
     * Finds a push notification by the specific key that is passed through
     *
     * @param string $key
     * @return PushNotificationKey|null
     */
    public function findByPushNotificationKey(string $key): ?PushNotificationKey;
}
