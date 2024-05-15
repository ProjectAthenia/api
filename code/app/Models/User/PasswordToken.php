<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PasswordToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\PasswordToken newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\PasswordToken newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\PasswordToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\PasswordToken whereUserId($value)
 * @mixin \Eloquent
 */
class PasswordToken extends BaseModelAbstract
{
    /**
     * The user relation to the user that generated this token
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Swagger definition below for a password token...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="PasswordToken",
     *     @SWG\Property(
     *         property="token",
     *         type="string",
     *         maxLength=120,
     *         description="The token that was generated."
     *     ),
     *     @SWG\Property(
     *         property="email",
     *         type="string",
     *         maxLength=120,
     *         description="The email address of the user the token is associated with."
     *     ),
     * )
     */
}