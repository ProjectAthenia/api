<?php
declare(strict_types=1);

namespace App\Models\Payment;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Models\Subscription\Subscription;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class PaymentMethod
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string|null $payment_method_key
 * @property string $payment_method_type
 * @property string|null $identifier
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $default
 * @property string|null $brand
 * @property string|null $exp_month
 * @property string|null $exp_year
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\PaymentMethod newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\PaymentMethod newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereExpMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereExpYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod wherePaymentMethodKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod wherePaymentMethodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\PaymentMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentMethod extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * All payments that have been made with this payment method
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * All subscriptions that renew with this payment method
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * A payment method will have a morph to relation to the owner of the payment method
     *
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'token' => [
                    'string',
                    'max:120',
                ],
                'default' => [
                    'boolean',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'token',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'token',
                ],
            ],
        ];
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="PaymentMethod",
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
     *         property="payment_method_key",
     *         type="string",
     *         maxLength=120,
     *         description="The key for the payment method on the remote server",
     *     ),
     *     @SWG\Property(
     *         property="payment_method_type",
     *         type="string",
     *         maxLength=120,
     *         description="The type of payment method this is. This refers to the the payment service.",
     *     ),
     *     @SWG\Property(
     *         property="user_id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the user that this payment method is related to",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="user",
     *         description="The users that this was sent to.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/User")
     *     )
     * )
     */
}
