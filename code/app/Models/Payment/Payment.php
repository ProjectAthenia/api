<?php
declare(strict_types=1);

namespace App\Models\Payment;

use App\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Payment
 *
 * @property int $id
 * @property int $payment_method_id
 * @property float $amount
 * @property string|null $transaction_key
 * @property \Illuminate\Support\Carbon|null $refunded_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $owner_id
 * @property string|null $owner_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\LineItem[] $lineItems
 * @property-read int|null $line_items_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @property-read \App\Models\Payment\PaymentMethod $paymentMethod
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\Payment newModelQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\Payment newQuery()
 * @method static \AdminUI\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Payment\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereTransactionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment\Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends BaseModelAbstract
{
    /**
     * @var array All custom dates
     */
    protected $casts = [
        'refunded_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];

    /**
     * The items paid for
     *
     * @return HasMany
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    /**
     * The owner of the payment
     *
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The payment method that this payment was made with
     *
     * @return BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}