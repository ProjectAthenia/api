<?php
declare(strict_types=1);

namespace App\Models\Subscription;

use App\Athenia\Contracts\Models\HasPaymentsContract;
use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Models\Traits\HasPayments;
use App\Athenia\Models\Traits\HasValidationRules;
use App\Athenia\Validators\Subscription\MembershipPlanRateIsActiveValidator;
use App\Athenia\Validators\Subscription\PaymentMethodIsOwnedByEntityValidator;
use App\Models\Payment\PaymentMethod;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\Rule;

/**
 * Class Subscription
 *
 * @property int $id
 * @property int $membership_plan_rate_id
 * @property int $payment_method_id
 * @property int $subscriber_id
 * @property string $subscriber_type
 * @property mixed|null $last_renewed_at
 * @property mixed|null $subscribed_at
 * @property mixed|null $expires_at
 * @property mixed|null $canceled_at
 * @property bool $recurring
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_trial
 * @property-read null|string $formatted_cost
 * @property-read null|string $formatted_expires_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\LineItem[] $lineItems
 * @property-read int|null $line_items_count
 * @property-read \App\Models\Subscription\MembershipPlanRate $membershipPlanRate
 * @property-read \App\Models\Payment\PaymentMethod $paymentMethod
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subscriber
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\Subscription newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\Subscription newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Subscription\Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereIsTrial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereLastRenewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereMembershipPlanRateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereSubscribedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereSubscriberType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription\Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends BaseModelAbstract implements HasValidationRulesContract, HasPaymentsContract
{
    use HasValidationRules, HasPayments;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_renewed_at' => 'datetime:c',
        'subscribed_at' => 'datetime:c',
        'expires_at' => 'datetime:c',
        'canceled_at' => 'datetime:c',
    ];

    /**
     * The membership plan rate this subscription is signed up for
     *
     * @return BelongsTo
     */
    public function membershipPlanRate(): BelongsTo
    {
        return $this->belongsTo(MembershipPlanRate::class);
    }

    /**
     * The payment method that is used to renew this subscription
     *
     * @return BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * The subscriber this subscription is for
     *
     * @return MorphTo
     */
    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @inheritDoc
     */
    public function morphRelationName(): string
    {
        return 'subscription';
    }

    /**
     * Determines whether or not this subscription is good for a lifetime
     *
     * @return bool
     */
    public function isLifetime() : bool
    {
        return $this->membershipPlanRate->membershipPlan->duration == MembershipPlan::DURATION_LIFETIME;
    }

    /**
     * Formats the expires at date string
     *
     * @return null|string
     */
    public function getFormattedExpiresAtAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('F jS Y') : null;
    }

    /**
     * Formats the cost to be human readable
     *
     * @return null|string
     */
    public function getFormattedCostAttribute()
    {
        return $this->membershipPlanRate && $this->membershipPlanRate->cost ?
            number_format((float)$this->membershipPlanRate->cost, 2) : null;
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'cancel' => [
                    'boolean',
                ],
                'membership_plan_rate_id' => [
                    'integer',
                    Rule::exists('membership_plan_rates', 'id'),
                    MembershipPlanRateIsActiveValidator::KEY,
                ],
                'payment_method_id' => [
                    'integer',
                    Rule::exists('payment_methods', 'id'),
                    PaymentMethodIsOwnedByEntityValidator::KEY,
                ],
                'is_trial' => [
                    'boolean',
                ],
                'recurring' => [
                    'boolean',
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED_UNLESS . 'is_trial,true' => [
                    'payment_method_id',
                ],
                self::VALIDATION_PREPEND_REQUIRED => [
                    'membership_plan_rate_id',
                ],
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'cancel',
                ],
            ],
            self::VALIDATION_RULES_UPDATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'membership_plan_rate_id',
                    'is_trial',
                ],
            ],
        ];
    }
}
