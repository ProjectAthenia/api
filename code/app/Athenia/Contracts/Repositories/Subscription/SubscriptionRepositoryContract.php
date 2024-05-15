<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Subscription;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface SubscriptionRepositoryContract
 * @package App\Contracts\Repositories\Subscription
 */
interface SubscriptionRepositoryContract extends BaseRepositoryContract
{
    /**
     * Finds all subscriptions that expire at a certain date
     *
     * @param Carbon $expiresAt
     * @return Collection
     */
    public function findExpiring(Carbon $expiresAt): Collection;

    /**
     * Finds all subscriptions that expire after the passed in expiration date
     *  The optional type field will filter out all subscriptions that are not to a specific subscriber type
     *
     * @param Carbon $expirationDate
     * @param string|null $type
     * @return Collection
     */
    public function findExpiresAfter(Carbon $expirationDate, string $type = null): Collection;
}