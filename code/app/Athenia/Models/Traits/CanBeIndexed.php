<?php
declare(strict_types=1);

namespace App\Athenia\Models\Traits;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait CanBeIndexed
 * @package App\Models\Traits
 */
trait CanBeIndexed
{
    /**
     * The resource object for this indexable model
     *
     * @return MorphOne
     */
    public function resource() : MorphOne
    {
        return $this->morphOne(Resource::class, 'resource');
    }
}