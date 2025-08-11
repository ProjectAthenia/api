<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Models\Asset;
use App\Models\Organization\Organization;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ProfileImage
 *
 * @property int $id
 * @property int|null $owner_id
 * @property string|null $name
 * @property string|null $caption
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string|null $owner_type
 * @property-read \App\Models\Organization\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @property-read \App\Models\User\User|null $user
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\ProfileImage newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\ProfileImage newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\ProfileImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\ProfileImage whereUrl($value)
 * @mixin \Eloquent
 */
class ProfileImage extends Asset
{
    /**
     * @return HasOne
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class);
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
