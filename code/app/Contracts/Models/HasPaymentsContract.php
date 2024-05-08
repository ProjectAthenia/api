<?php
declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Interface HasPaymentsContract
 * @package App\Contracts\Models
 */
interface HasPaymentsContract extends CanBeMorphedToContract
{
    /**
     * THe line items that are related to this model.
     * These act as the go between from this item and associated payments
     *
     * @return MorphMany
     */
    public function lineItems(): MorphMany;

    /**
     * The payments related to this model.
     *
     * @return MorphToMany
     */
    public function payments(): MorphToMany;
}