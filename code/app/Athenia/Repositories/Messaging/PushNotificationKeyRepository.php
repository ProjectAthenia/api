<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Messaging;

use App\Athenia\Contracts\Repositories\Messaging\PushNotificationKeyRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Messaging\PushNotificationKey;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PushNotificationKeyRepository
 * @package App\Repositories\User
 */
class PushNotificationKeyRepository extends BaseRepositoryAbstract implements PushNotificationKeyRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Delete, \App\Athenia\Repositories\Traits\NotImplemented\FindAll, \App\Athenia\Repositories\Traits\NotImplemented\FindOrFail;

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
