<?php
declare(strict_types=1);

namespace App\Models\Messaging;

use App\Contracts\Models\Messaging\CanReceivePushNotificationContract;
use App\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class PushNotifications
 *
 * @property int $id
 * @property int $user_id
 * @property string $push_notification_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read CanReceivePushNotificationContract $owner
 * @mixin Eloquent
 */
class PushNotificationKey extends BaseModelAbstract
{
    /**
     * @var string Table override due to laravel bug
     */
    protected $table = 'push_notification_keys';

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }
}
