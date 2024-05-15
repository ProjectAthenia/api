<?php
declare(strict_types=1);

namespace App\Athenia\Models\Traits;

use App\Models\Collection\Collection;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait IsEntity
 * @package App\Models\Traits
 */
trait IsEntity
{
    /**
     * All payment methods owned by this model
     *
     * @return MorphMany
     */
    public function collections(): MorphMany
    {
        return $this->morphMany(Collection::class, 'owner');
    }

    /**
     * All payment methods owned by this model
     *
     * @return MorphMany
     */
    public function paymentMethods(): MorphMany
    {
        return $this->morphMany(PaymentMethod::class, 'owner');
    }

    /**
     * All payment methods owned by this model
     *
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'owner');
    }

    /**
     * All Subscriptions this subscriber has signed up to
     *
     * @return MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscriber');
    }

    /**
     * Leads the users current active subscription if there is one
     *
     * @param Carbon|null $expiresAfter
     * @return Subscription|null
     */
    public function currentSubscription(?Carbon $expiresAfter = null) : ?Subscription
    {
        return $this->subscriptions->sortByDesc('created_at')->first(function (Subscription $subscription) use ($expiresAfter) {
            $expiresAfter = $expiresAfter ?? Carbon::now();
            return $subscription->isLifetime() ? true :
                ($subscription->expires_at ? $subscription->expires_at->greaterThan($expiresAfter) : false);
        });
    }
}
