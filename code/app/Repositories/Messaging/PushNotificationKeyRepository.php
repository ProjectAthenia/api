<?php
declare(strict_types=1);

namespace App\Repositories\Messaging;

use App\Models\Messaging\PushNotificationKey;
use App\Repositories\BaseRepositoryAbstract;
use App\Contracts\Repositories\Messaging\PushNotificationKeyRepositoryContract;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PushNotificationKeyRepository
 * @package App\Repositories\User
 */
class PushNotificationKeyRepository extends BaseRepositoryAbstract implements PushNotificationKeyRepositoryContract
{
    use NotImplemented\Delete, NotImplemented\FindAll, NotImplemented\FindOrFail;

    /**
     * PushNotificationKeyRepository constructor.
     * @param PushNotificationKey $model
     * @param LogContract $log
     */
    public function __construct(PushNotificationKey $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Finds a push notification by the specific key that is passed through
     *
     * @param string $key
     * @return PushNotificationKey|null
     */
    public function findByPushNotificationKey(string $key): ?PushNotificationKey
    {
        return $this->model->newQuery()->where('push_notification_key', '=', $key)->first();
    }
}
