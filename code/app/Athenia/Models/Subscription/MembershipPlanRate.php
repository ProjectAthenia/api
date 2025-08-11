<?php
declare(strict_types=1);

namespace App\Models\Subscription;

use App\Athenia\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MembershipPlanRate
 *
 * @property int $id
 * @property int $membership_plan_id
 * @property float $cost
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Subscription\MembershipPlan $membershipPlan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\MembershipPlanRate newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\MembershipPlanRate newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\MembershipPlanRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereMembershipPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\MembershipPlanRate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MembershipPlanRate extends BaseModelAbstract
{
    /**
     * The membership plan this is related to
     *
     * @return BelongsTo
     */
    public function membershipPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    /**
     * All subscriptions that have been signed up for this membership plan rate
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}