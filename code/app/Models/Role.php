<?php
declare(strict_types=1);

namespace App\Models;

use App\Athenia\Contracts\Models\HasPolicyContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 *
 * @property int $id
 * @property string $name
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User\User[] $users
 * @property-read int|null $users_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Role newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Role newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends BaseModelAbstract implements HasPolicyContract
{
    const APP_USER = 1;
    const SUPER_ADMIN = 2;
    const ARTICLE_VIEWER = 3;
    const ARTICLE_EDITOR = 4;
    const ADMINISTRATOR = 10;
    const MANAGER = 11;
    // Add more roles here start with 100 in order to avoid application collision

    /**
     * @var array[string] the roles (usefully mainly for testing I suppose)
     */
    const ROLES = [
        self::APP_USER,
        self::SUPER_ADMIN,
        self::ARTICLE_VIEWER,
        self::ARTICLE_EDITOR,
        // Add application specific roles here too
    ];

    /**
     * All roles that are related to an organization
     */
    const ENTITY_ROLES = [
        self::ADMINISTRATOR,
        self::MANAGER,
        // Add more organization roles here
    ];

    /**
     * Has many users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="Role",
     *     @SWG\Property(
     *         property="id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the model",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was created",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was last updated",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="name",
     *         type="string",
     *         maxLength=32,
     *         description="The name of the role"
     *     ),
     * )
     */
}
