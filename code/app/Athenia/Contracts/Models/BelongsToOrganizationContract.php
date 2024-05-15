<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Interface BelongsToOrganizationContract
 * @package App\Contracts\Models
 * @property $organization_id
 */
interface BelongsToOrganizationContract
{
    /**
     * The organization this model belongs to
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo;
}