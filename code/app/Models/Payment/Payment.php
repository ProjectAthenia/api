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
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Payment newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Payment newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Payment extends BaseModelAbstract
{
    /**
     * @var array All custom dates
     */
    protected $dates = [
        'refunded_at',
        'deleted_at',
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