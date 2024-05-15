<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models;

use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Interface CanHaveMultipleOwnerTypes
 * @package App\Contracts\Models
 * @property int $id
 * @property PaymentMethod[]|Collection $paymentMethods
 * @property \App\Models\Collection\Collection[]|Collection $collections
 */
interface IsAnEntityContract extends CanBeMorphedToContract
{
    /**
     * Tells us whether or not the logged in user can manage this entity
     *
     * @param User $user The logged in user
     * @param int $role An optional role id that we are checking for
     * @return bool
     */
    public function canUserManageEntity(User $user, int $role = Role::MANAGER): bool;

    /**
     * This is for the relation for the collections the entity owns
     *
     * @return MorphMany
     */
    public function collections(): MorphMany;

    /**
     * This is for the relation for the payment methods
     *
     * @return MorphMany
     */
    public function paymentMethods(): MorphMany;

    /**
     * All payments this entity has made
     *
     * @return MorphMany
     */
    public function payments(): MorphMany;


    /**
     * All Subscriptions this subscriber has signed up to
     *
     * @return MorphMany
     */
    public function subscriptions(): MorphMany;

    /**
     * Leads the users current active subscription if there is one
     *
     * @param Carbon|null $expiresAfter
     * @return Subscription|null
     */
    public function currentSubscription(?Carbon $expiresAfter = null) : ?Subscription;
}
