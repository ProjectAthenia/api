<?php
declare(strict_types=1);

namespace App\Athenia\Models\Traits;

use App\Models\Organization\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait BelongsToOrganization
 * @package App\Models\Traits
 */
trait BelongsToOrganization
{
    /**
     * The organization this device belongs to
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}