<?php
declare(strict_types=1);

namespace App\Athenia\Models\Traits;

use App\Models\Payment\LineItem;
use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait HasPayments
 * @package App\Models\Traits
 */
trait HasPayments
{
    /**
     * The purchased item instances for this subscription
     *
     * @return MorphMany
     */
    public function lineItems(): MorphMany
    {
        return $this->morphMany(LineItem::class, 'item');
    }

    /**
     * The payments that have been made for this subscription
     *
     * @return MorphToMany
     */
    public function payments(): MorphToMany
    {
        return $this->morphToMany(Payment::class, 'item', 'line_items');
    }
}