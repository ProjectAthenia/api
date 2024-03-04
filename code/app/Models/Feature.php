<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Subscription\MembershipPlan;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Feature
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\MembershipPlan[] $membershipPlans
 * @property-read int|null $membership_plans_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Feature newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Feature newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Feature extends BaseModelAbstract
{
    /**
     * @return BelongsToMany
     */
    public function membershipPlans(): BelongsToMany
    {
        return $this->belongsToMany(MembershipPlan::class);
    }
}
